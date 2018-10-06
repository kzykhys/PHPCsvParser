<?php

use KzykHys\CsvParser\Iterator\CsvIterator;

class CsvIteratorTest extends PHPUnit_Framework_TestCase
{

    public function testBlank()
    {
        $iterator = new CsvIterator(new ArrayIterator());
        $result   = iterator_to_array($iterator);

        $this->assertEquals(array(), $result);
    }

    public function testArrayKey()
    {
        $iterator = new CsvIterator(new ArrayIterator(array('1,2')), array('header' => array('a', 'b')));
        $result = iterator_to_array($iterator);

        $this->assertEquals(array(array('a' => 1, 'b' => 2)), $result);
    }

}