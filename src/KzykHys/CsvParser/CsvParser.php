<?php

namespace KzykHys\CsvParser;

use KzykHys\CsvParser\Iterator\CsvIterator;
use KzykHys\CsvParser\Iterator\FileIterator;

/**
 * Parse CP932 encoded CSV lines
 *
 * @author Kazuyuki Hayashi <hayashi@valnur.net>
 */
class CsvParser implements \IteratorAggregate
{

    /**
     * @var Iterator\CsvIterator
     */
    private $iterator;

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

        return new self(new FileIterator($file), $option);
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
        $lines = array();

        if ($csv !== '') {
            if (!preg_match('/(\r|\n|\r\n)\Z/m', $csv)) {
                $csv .= "\n";
            }

            preg_match_all('/[^\r\n]*(?:\r|\n|\r\n)+/m', $csv, $matches);

            $lines = $matches[0];
        }

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
        $option = array_merge(array(
            'offset' => 0,
            'limit'  => -1
        ), $option);

        $this->iterator = new CsvIterator($csv, $option);

        if ($option['offset'] > 0 || $option['limit'] > -1) {
            $this->iterator = new \LimitIterator(
                new \CachingIterator($this->iterator, \CachingIterator::FULL_CACHE), $option['offset'], $option['limit']
            );
        }
    }

    /**
     * Parse CSV lines
     *
     * @return array
     */
    public function parse()
    {
        return iterator_to_array($this->iterator);
    }

    /**
     * Retrieve an external iterator
     *
     * @link http://php.net/manual/en/iteratoraggregate.getiterator.php
     *
     * @return \Traversable An instance of an object implementing <b>Iterator</b> or <b>Traversable</b>
     */
    public function getIterator()
    {
        return $this->iterator;
    }

}