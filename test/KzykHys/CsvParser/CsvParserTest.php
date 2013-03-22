<?php


class CsvParserTest extends PHPUnit_Framework_TestCase
{

    public function testParserFromFile()
    {
        $parser = \KzykHys\CsvParser\CsvParser::fromFile(__DIR__.'/CsvParserTest.csv');
        $result = $parser->parse();

        var_dump($result);
    }

    public function testParserFromString()
    {
        $parser = \KzykHys\CsvParser\CsvParser::fromString(file_get_contents(__DIR__.'/CsvParserTest.csv'));
        $result = $parser->parse();

        var_dump($result);
    }

    public function testIteratorAggregate()
    {
        var_dump(__METHOD__);

        $parser = \KzykHys\CsvParser\CsvParser::fromFile(__DIR__.'/CsvParserTest.csv');

        foreach ($parser as $record) {
            var_dump($record);
        }
    }

    public function testLargeCsv()
    {
        $parser = \KzykHys\CsvParser\CsvParser::fromFile(__DIR__.'/LargeCSV.csv');
        foreach ($parser as $record) {
            $this->assertEquals("9500000", $record[2]);
            $this->assertEquals("新潟県", $record[6]);
            break;
        }
    }

}