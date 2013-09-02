<?php


class FileIteratorTest extends \PHPUnit_Framework_TestCase
{

    public function testIterator()
    {
        $iterator = new \KzykHys\CsvParser\Iterator\FileIterator(__DIR__.'/../Resources/csv/basic.utf8.csv');

        $result = iterator_to_array($iterator);

        $this->assertEquals(array(
            '1,"The String",3,2012-11-15,9'."\n",
            '2,"The Multi-line'."\n",
            'String",192818281211212212,"2012-11-15","ABC"'."\n"
        ), $result);
    }

    public function testIteratorOnBlankFile()
    {
        $iterator = new \KzykHys\CsvParser\Iterator\FileIterator(__DIR__.'/../Resources/csv/blank.csv');
        $index = 0;

        $this->assertFalse($iterator->valid());

        foreach ($iterator as $test) {
            $index++;
        }

        $this->assertEquals(0, $index);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testThrowExceptionIfFileIsInvalid()
    {
        new \KzykHys\CsvParser\Iterator\FileIterator(__DIR__.'/../Resources/csv/foo.csv');
    }

}