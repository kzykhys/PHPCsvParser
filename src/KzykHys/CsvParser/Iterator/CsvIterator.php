<?php

namespace KzykHys\CsvParser\Iterator;

/**
 * Iterator for converting csv lines to array
 *
 * @author Kazuyuki Hayashi <hayashi@valnur.net>
 */
class CsvIterator implements \Iterator
{

    /**
     * @var \Iterator $iterator
     */
    private $iterator;

    /**
     * @var array $option
     */
    private $option = array();

    /**
     * @var int $key Current index
     */
    private $key = 0;

    /**
     * @var string $revert
     */
    private $revert = '';

    /**
     * @var bool $continue
     */
    private $continue = false;

    /**
     * @var array $result
     */
    private $result = array();

    /**
     * @var int $col
     */
    private $col = 0;

    /**
     * @param \Iterator $iterator
     * @param array     $option
     */
    public function __construct(\Iterator $iterator, array $option = array())
    {
        $this->iterator = $iterator;
        $this->option = array_merge(array(
            'delimiter' => ',',
            'enclosure' => '"',
            'encoding'  => 'CP932',
            'header'    => false
        ), $option);
    }

    /**
     * Return the current element
     *
     * @link http://php.net/manual/en/iterator.current.php
     *
     * @return mixed Can return any type.
     */
    public function current()
    {
        $this->result = array();

        // revert necessary delimiter
        $this->revert = $this->option['delimiter'];

        // loop over the lines
        while ($this->iterator->valid()) {
            $line = $this->iterator->current();
            $line = mb_convert_encoding($line, 'UTF-8', isset($this->option['encoding']) ? $this->option['encoding'] : 'auto');

            // split the line by 'delimiter'
            $tokens = explode($this->option['delimiter'], $line);

            // loop over the columns
            foreach ($tokens as $value) {
                $value = preg_replace('/"(\r\n|\r|\n)*$/', '"', $value);

                // check the first letter is 'enclosure' or not
                if (substr($value, 0, 1) == $this->option['enclosure']) {
                    // check the last letter is 'enclosure'
                    if (substr($value, -1) == $this->option['enclosure']) {
                        $this->processEnclosedField($value, $this->option);
                    } else {
                        $this->processContinuousField($value, $this->option);
                    }

                } else { // first letter is NOT 'enclosure'
                    // check the last letter is 'enclosure'
                    if(substr($value, -1) == $this->option['enclosure']) {
                        $this->processClosingField($value, $this->option);
                    } else {
                        $this->processField($value, $this->option);
                    }
                }

                if ($this->revert == "") {
                    $this->revert = $this->option['delimiter'];
                }
            }

            // If the cell is closed, reset the column index and go to next row.
            if(!$this->continue) {
                $this->col = 0;
                break;
            }

            $this->revert = "";
            $this->iterator->next();
        }

        return $this->result;
    }

    /**
     * Process enclosed field
     *
     * example: "value"
     *
     * @param string $value  Current token
     * @param array  $option Option
     */
    private function processEnclosedField($value, array $option)
    {
        // then, remove enclosure and line feed
        $cell = $this->trimEnclosure($value, $option['enclosure']);
        // replace the escape sequence "" to "
        $cell = $this->unescapeEnclosure($cell, $option['enclosure']);

        $this->setCell($cell);
        $this->col++;
        $this->continue = false;
    }

    /**
     * Process enclosed and multiple line field
     *
     * example: "value\n
     *
     * @param string $value  Current token
     * @param array  $option Option
     */
    private function processContinuousField($value, array $option)
    {
        $cell = $this->trimLeftEnclosure($value, $option['enclosure']);
        $cell = $this->unescapeEnclosure($cell, $option['enclosure']);

        $this->setCell($cell);
        $this->continue = true;
    }

    /**
     * Process end of enclosure
     *
     * example: value"
     *
     * If previous token was not closed, this token is joined,
     * otherwise this token is a new cell.
     *
     * @param string $value  Current token
     * @param array  $option Option
     */
    private function processClosingField($value, array $option)
    {
        $cell = $this->trimRightEnclosure($value, $option['enclosure']);
        $cell = $this->unescapeEnclosure($cell, $option['enclosure']);

        $this->joinCell($this->revert . $cell);
        $this->continue = false;
        $this->col++;
    }

    /**
     * Process unenclosed field
     *
     * example: value
     *
     * @param string $value  Current token
     * @param array  $option Option
     */
    private function processField($value, array $option)
    {
        if($this->continue) {
            $cell = $this->unescapeEnclosure($value, $option['enclosure']);
            $this->joinCell($this->revert . $cell);
        } else {
            $cell = rtrim($value);
            $cell = $this->unescapeEnclosure($cell, $option['enclosure']);
            $this->setCell($cell);
            $this->col++;
        }
    }

    /**
     * Set value to current cell
     *
     * @param string $cell
     */
    private function setCell($cell)
    {
        $this->result[$this->getIndexForColumn($this->col)] = $cell;
    }

    /**
     * Append value to current cell
     *
     * @param string $cell
     */
    private function joinCell($cell)
    {
        $this->result[$this->getIndexForColumn($this->col)] .= $cell;
    }

    /**
     * Convert double enclosure to single enclosure
     *
     * @param $value
     * @param $enclosure
     *
     * @return mixed
     */
    private function unescapeEnclosure($value, $enclosure)
    {
        return str_replace(str_repeat($enclosure, 2), $enclosure, $value);
    }

    /**
     * String enclosure string from beginning and end of the string
     *
     * @param string $value
     * @param string $enclosure
     * @return string
     */
    private function trimEnclosure($value, $enclosure)
    {
        $value = $this->trimLeftEnclosure($value, $enclosure);
        $value = $this->trimRightEnclosure($value, $enclosure);

        return $value;
    }

    /**
     * Strip an enclosure string from beginning of the string
     *
     * @param string $value
     * @param string $enclosure
     * @return string
     */
    private function trimLeftEnclosure($value, $enclosure)
    {
        if (substr($value, 0, 1) == $enclosure) {
            $value = substr($value, 1);
        }

        return $value;
    }

    /**
     * Strip an enclosure string from end of the string
     *
     * @param string $value
     * @param string $enclosure
     * @return string
     */
    private function trimRightEnclosure($value, $enclosure)
    {
        if (substr($value, -1) == $enclosure) {
            $value = substr($value, 0, -1);
        }

        return $value;
    }

    /**
     * @param $column
     */
    private function getIndexForColumn($column)
    {
        if (is_array($this->option['header']) && isset($this->option['header'][$column])) {
            return $this->option['header'][$column];
        }

        return $column;
    }

    /**
     * Move forward to next element
     *
     * @link http://php.net/manual/en/iterator.next.php
     *
     * @return void Any returned value is ignored.
     */
    public function next()
    {
        $this->iterator->next();
        $this->key++;
    }

    /**
     * Return the key of the current element
     *
     * @link http://php.net/manual/en/iterator.key.php
     *
     * @return mixed scalar on success, or null on failure.
     */
    public function key()
    {
        return $this->key;
    }

    /**
     * Checks if current position is valid
     *
     * @link http://php.net/manual/en/iterator.valid.php
     *
     * @return boolean The return value will be casted to boolean and then evaluated.
     *          Returns true on success or false on failure.
     */
    public function valid()
    {
        return $this->iterator->valid();
    }

    /**
     * Rewind the Iterator to the first element
     *
     * @link http://php.net/manual/en/iterator.rewind.php
     *
     * @return void Any returned value is ignored.
     */
    public function rewind()
    {
        $this->iterator->rewind();
        $this->key = 0;
    }

}