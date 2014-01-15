<?php

namespace KzykHys\CsvParser\Iterator;

/**
 * @author Kazuyuki Hayashi <hayashi@valnur.net>
 */
class FileIterator implements \Iterator
{

    /**
     * @var resource
     */
    private $handle;

    /**
     * @var int
     */
    private $fileSize = 0;

    /**
     * @var int
     */
    private $offset = 0;

    /**
     * @var int
     */
    private $length = 0;

    /**
     * @var int
     */
    private $index = 0;

    /**
     * @var string
     */
    private $defaultAutoDetectLineEndings;

    /**
     * Constructor
     *
     * @param $path
     *
     * @throws \InvalidArgumentException
     */
    public function __construct($path)
    {
        if (!file_exists($path)) {
            throw new \InvalidArgumentException('File not found: ' . $path);
        }

        $this->defaultAutoDetectLineEndings = ini_get('auto_detect_line_endings');
        ini_set("auto_detect_line_endings", 1);

        $stat = stat($path);
        $this->fileSize = $stat['size'];
        $this->handle = fopen($path, 'r');
    }

    /**
     * Destructor
     */
    public function __destruct()
    {
        if ($this->handle) {
            fclose($this->handle);
        }

        ini_set("auto_detect_line_endings", $this->defaultAutoDetectLineEndings);
    }

    /**
     * Return the current element
     *
     * @return mixed Can return any type.
     */
    public function current()
    {
        $line = fgets($this->handle);
        $this->length = strlen($line);
        fseek($this->handle, $this->offset);

        return $line;
    }

    /**
     * Move forward to next element
     *
     * @return void Any returned value is ignored.
     */
    public function next()
    {
        $this->offset += $this->length;
        fseek($this->handle, $this->offset);
        $this->index++;
    }

    /**
     * Return the key of the current element
     *
     * @return mixed scalar on success, or null on failure.
     */
    public function key()
    {
        return $this->index;
    }

    /**
     * Checks if current position is valid
     *
     * @return boolean The return value will be casted to boolean and then evaluated.
     *                  Returns true on success or false on failure.
     */
    public function valid()
    {
        if ($this->fileSize == 0) {
            return false;
        }

        if (feof($this->handle)) {
            return false;
        }

        $current = fgets($this->handle);

        if (trim($current) === '' && feof($this->handle)) {
            return false;
        } else {
            fseek($this->handle, $this->offset);
        }

        return true;
    }

    /**
     * Rewind the Iterator to the first element
     *
     * @return void Any returned value is ignored.
     */
    public function rewind()
    {
        rewind($this->handle);
        $this->index = 0;
        $this->offset = 0;
        $this->length = 0;
    }

}