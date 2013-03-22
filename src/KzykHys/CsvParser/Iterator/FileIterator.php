<?php

namespace KzykHys\CsvParser\Iterator;

/**
 * Iterator for thin wrap of `fopen`
 *
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
    private $key = 0;

    /**
     * Constructor
     *
     * @param $file
     *
     * @throws \InvalidArgumentException
     */
    public function __construct($file)
    {
        if (!file_exists($file)) {
            throw new \InvalidArgumentException('File not found: ' . $file);
        }

        $this->handle = fopen($file, 'r');
    }

    /**
     * Destructor
     */
    public function __destruct()
    {
        if ($this->handle) {
            fclose($this->handle);
        }
    }

    /**
     * Return the current element
     *
     * @link http://php.net/manual/en/iterator.current.php
     * @return mixed Can return any type.
     */
    public function current()
    {
        $this->key++;

        return fgets($this->handle);
    }

    /**
     * Move forward to next element
     *
     * @link http://php.net/manual/en/iterator.next.php
     * @return void Any returned value is ignored.
     */
    public function next()
    {
        // TODO: Implement next() method.
    }

    /**
     * Return the key of the current element
     *
     * @link http://php.net/manual/en/iterator.key.php
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
     * @return boolean The return value will be casted to boolean and then evaluated.
     *                  Returns true on success or false on failure.
     */
    public function valid()
    {
        return !feof($this->handle);
    }

    /**
     * Rewind the Iterator to the first element
     *
     * @link http://php.net/manual/en/iterator.rewind.php
     * @return void Any returned value is ignored.
     */
    public function rewind()
    {
        rewind($this->handle);
    }

}