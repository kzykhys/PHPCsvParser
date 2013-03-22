<?php


class CsvIteratorTest extends PHPUnit_Framework_TestCase
{

    public function testIterator()
    {
        $csv = new \KzykHys\CsvParser\Iterator\FileIterator(__DIR__.'/../CsvParserTest.csv');

        $iterator = new \KzykHys\CsvParser\Iterator\CsvIterator($csv);

        foreach ($iterator as $record) {
            var_dump($record);
        }
    }

    public function testParser()
    {
        $csv = new \KzykHys\CsvParser\Iterator\FileIterator(__DIR__.'/../CsvParserTest.csv');

        $parser = new \KzykHys\CsvParser\CsvParser($csv);
        $result = $parser->parse();

        var_dump($result);
    }

}