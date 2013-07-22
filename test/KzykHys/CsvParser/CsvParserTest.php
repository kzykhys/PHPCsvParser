<?php

class CsvParserTest extends \PHPUnit_Framework_TestCase
{

    public function testParser()
    {
        $iterator = new SplFileObject(__DIR__.'/Resources/csv/basic.utf8.csv');
        $parser = new \KzykHys\CsvParser\CsvParser($iterator);

        $result = $parser->parse();

        $this->assertEquals(array(
            array('1', 'The String', '3', '2012-11-15', '9'),
            array('2', "The Multi-line\nString", '192818281211212212', '2012-11-15', 'ABC')
        ), $result);
    }

    public function testParserFromString()
    {
        $csv = <<<EOF
100,101,102,103
200,201,202,203
EOF;

        $parser = \KzykHys\CsvParser\CsvParser::fromString($csv);
        $result = $parser->parse();

        $this->assertEquals(array(array(100,101,102,103), array(200,201,202,203)), $result);
    }

    public function testParserFromArray()
    {
        $csv = array('100,101,102,103'."\r\n", '200,201,202,203'."\r\n");

        $parser = \KzykHys\CsvParser\CsvParser::fromArray($csv);
        $result = $parser->parse();

        $this->assertEquals(array(array(100,101,102,103), array(200,201,202,203)), $result);
    }

    public function testParserFromFile()
    {
        $parser = \KzykHys\CsvParser\CsvParser::fromFile(__DIR__.'/Resources/csv/basic.utf8.csv');
        $result = $parser->parse();
        $this->assertEquals(array(
            array('1', 'The String', '3', '2012-11-15', '9'),
            array('2', "The Multi-line\nString", '192818281211212212', '2012-11-15', 'ABC')
        ), $result);
    }

    public function testIterateParser()
    {
        $parser = \KzykHys\CsvParser\CsvParser::fromFile(__DIR__.'/Resources/csv/basic.utf8.csv');
        $result = iterator_to_array($parser);

        $this->assertEquals(array(
            array('1', 'The String', '3', '2012-11-15', '9'),
            array('2', "The Multi-line\nString", '192818281211212212', '2012-11-15', 'ABC')
        ), $result);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testInvalidArgumentException()
    {
        $parser = \KzykHys\CsvParser\CsvParser::fromFile('foo.csv');
    }

    public function testFileContainsMultipleLineField()
    {
        $parser = \KzykHys\CsvParser\CsvParser::fromFile(__DIR__.'/Resources/csv/multiline.utf8.csv');
        $result = $parser->parse();

        $this->assertEquals(array(
            array('1', 'The text', "The multiline text\ncontains a few lines.\nEND_OF_FIELD", '150', '2012-12-12')
        ), $result);
    }

    public function testHandleLargeFile()
    {
        // large_file.cp932.csv is from Japan Post
        $parser = \KzykHys\CsvParser\CsvParser::fromFile(__DIR__.'/Resources/csv/large_file.cp932.csv');

        foreach ($parser as $line) {
        }
    }

    public function testBlankInputFromString()
    {
        $parser = \KzykHys\CsvParser\CsvParser::fromString("");
        $result = $parser->parse();

        $this->assertEquals(array(), $result);
    }

    public function testBlankInputFromArray()
    {
        $parser = \KzykHys\CsvParser\CsvParser::fromArray(array());
        $result = $parser->parse();

        $this->assertEquals(array(), $result);
    }

    public function testBlankInputFromFile()
    {
        $parser = \KzykHys\CsvParser\CsvParser::fromFile(__DIR__.'/Resources/csv/blank.csv');
        $result = $parser->parse();

        $this->assertEquals(array(), $result);

        $parser = \KzykHys\CsvParser\CsvParser::fromFile(__DIR__.'/Resources/csv/only_line_breaks.csv');
        $result = $parser->parse();

        $this->assertEquals(array(), $result);
    }

}