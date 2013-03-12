<?php

namespace KzykHys\CsvParser;

/**
 * Parse CP932 encoded CSV lines
 *
 * @author Kazuyuki Hayashi <hayashi@valnur.net>
 */
class CsvParser
{

    /**
     * @var array $csv The lines of CSV to parse
     */
    private $csv = array();

    /**
     * @var array Options
     */
    private $option = array();

    /**
     * @var int current row
     */
    private $row = 0;

    /**
     * @var int current column
     */
    private $col = 0;

    /**
     * @var array parsed value
     */
    private $result = array();

    /**
     * @var bool True if current cell is open
     */
    private $continue = false;

    /**
     * @var string The enclosure to revert
     */
    private $revert = '';

    /**
     * Returns new instance from CSV file
     *
     * @param string $file   The CSV string to parse
     * @param array  $option Options
     *
     * @throws \InvalidArgumentException
     *
     * @return CsvParser
     */
    public static function fromFile($file, array $option = array())
    {
        if (!file_exists($file)) {
            throw new \InvalidArgumentException('File not found: ' . $file);
        }

        return new self(new Iterator\FileIterator($file), $option);
    }

    /**
     * Returns new instance from string
     *
     * @param string $csv    The CSV string to parse
     * @param array  $option Options
     *
     * @return CsvParser
     */
    public static function fromString($csv, array $option = array())
    {
        $csv   = mb_convert_encoding($csv, 'UTF-8', isset($option['encoding']) ? $option['encoding'] : 'auto');
        $lines = preg_split('/\n/', $csv, -1, PREG_SPLIT_DELIM_CAPTURE);

        return self::fromArray($lines, $option);
    }

    /**
     * Returns new instance from array
     *
     * @param array  $csv    The lines of csv content
     * @param array  $option Options
     *
     * @return CsvParser
     */
    public static function fromArray(array $csv, array $option = array())
    {
        return new self(new \ArrayIterator($csv), $option);
    }

    /**
     * Constructor
     *
     * @param \Iterator $csv The lines of CSV to parse
     * @param array     $option
     */
    public function __construct(\Iterator $csv, array $option = array())
    {
        $this->csv = $csv;
        $this->option = array_merge(array(
            'delimiter' => ',',
            'enclosure' => '"',
            'encoding'  => 'CP932'
        ), $option);
    }

    /**
     * Parse CSV lines
     *
     * @return array
     */
    public function parse()
    {
        // revert necessary delimiter
        $this->revert = $this->option['delimiter'];

        // loop over the lines
        foreach ($this->csv as $line) {

            if (empty($line)) {
                continue;
            }

            // split the line by 'delimiter'
            $tokens = explode($this->option['delimiter'], $line);

            // loop over the columns
            foreach ($tokens as $value) {

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
                $this->row++;
                $this->col = 0;
            }

            $this->revert = "";
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
        $cell = rtrim(trim($value, $option['enclosure']));
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
        $cell = ltrim($value, $option['enclosure']);
        $cell = $this->unescapeEnclosure($cell, $option['enclosure']);

        if ($this->continue) {
            $this->joinCell($this->revert . $cell);
        } else {
            $this->setCell($cell);
        }

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
        $cell = rtrim($value, $option['enclosure']);
        $cell = $this->unescapeEnclosure($cell, $option['enclosure']);

        if($this->continue) {
            $this->joinCell($this->revert . $cell);
        } else {
            $this->setCell($cell);
        }

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
        $this->result[$this->row][$this->col] = $cell;
    }

    /**
     * Append value to current cell
     *
     * @param string $cell
     */
    private function joinCell($cell)
    {
        $this->result[$this->row][$this->col] .= $cell;
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

}