<?php

/**
 * Class FileIteratorTest
 */
class FileIteratorTest extends PHPUnit_Framework_TestCase
{

    public function testIterator()
    {
        $file = __DIR__ . '/FileIteratorTest.txt';

        $iterator = new \KzykHys\CsvParser\Iterator\FileIterator($file);

        foreach ($iterator as $key => $line) {
            var_dump($key);
            var_dump($line);
        }
    }

}