<?php

use KzykHys\CsvParser\Iterator\FileIterator;

class FileIteratorTest extends \PHPUnit_Framework_TestCase
{

    public function testIterator()
    {
        $iterator = new FileIterator(__DIR__.'/../Resources/csv/2-blank.csv');

        $this->assertFalse($iterator->valid());
        $this->assertEquals(0, iterator_count($iterator));
        $this->assertEquals(0, $iterator->key());

        $iterator = new FileIterator(__DIR__.'/../Resources/csv/1-plain.CRLF.csv');
        foreach ($iterator as $row);

        $this->assertFalse($iterator->valid());
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testThrowExceptionIfFileIsInvalid()
    {
        new FileIterator(__DIR__.'/../Resources/csv/foo.csv');
    }

}