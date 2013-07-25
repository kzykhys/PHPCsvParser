<?php


class CsvIteratorTest extends PHPUnit_Framework_TestCase
{

    public function testSplFileObject()
    {
        $file = __DIR__ . '/../Resources/csv/basic.utf8.csv';

        $iterator = new \KzykHys\CsvParser\Iterator\CsvIterator(new \SplFileObject($file));

        $result = iterator_to_array($iterator);

        $this->assertEquals(array(
            array('1', 'The String', '3', '2012-11-15', '9'),
            array('2', "The Multi-line\nString", '192818281211212212', '2012-11-15', 'ABC')
        ), $result);
    }

    public function testArray()
    {
        $test = array('1,2,3,4', '5,6,7,8');

        $iterator = new \KzykHys\CsvParser\Iterator\CsvIterator(new ArrayIterator($test));

        $result = iterator_to_array($iterator);

        $this->assertEquals(array(array(1, 2, 3, 4), array(5, 6, 7, 8)), $result);
    }

    public function testBlank()
    {
        $iterator = new \KzykHys\CsvParser\Iterator\CsvIterator(new ArrayIterator());

        $result = iterator_to_array($iterator);

        $this->assertEquals(array(), $result);
    }

    public function testNamedIndex()
    {
        $file = __DIR__ . '/../Resources/csv/basic.utf8.csv';

        $iterator = new \KzykHys\CsvParser\Iterator\CsvIterator(new \SplFileObject($file), array(
            'header' => array('ID', 'Text', 'Key', 'Date')
        ));

        $result = iterator_to_array($iterator);

        $this->assertEquals(array(
            array('ID' => '1', 'Text' => 'The String', 'Key' => '3', 'Date' => '2012-11-15', 4 => '9'),
            array('ID' => '2', 'Text' => "The Multi-line\nString", 'Key' => '192818281211212212', 'Date' => '2012-11-15', 4 => 'ABC')
        ), $result);
    }

}