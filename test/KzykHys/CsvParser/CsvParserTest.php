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

}