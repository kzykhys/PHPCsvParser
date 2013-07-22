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

        $parser = \KzykHys\CsvParser\CsvParser::fromFile(__DIR__.'/Resources/csv/multiline2.utf8.csv');
        $result = $parser->parse();

        $this->assertEquals(array(
            array('1', 'The text', "The multiline text\n\ncontains a few lines.\n\nEND_OF_FIELD", '150', '2012-12-12')
        ), $result);
    }

    public function testHandleLargeFile()
    {
        // large_file.cp932.csv is from Japan Post
        $parser = \KzykHys\CsvParser\CsvParser::fromFile(__DIR__.'/Resources/csv/large_file.cp932.csv');

        $index = 0;
        $result = array();
        foreach ($parser as $line) {
            if ($index++ == 3) {
                break;
            }

            $result[] = $line;
        }

        $this->assertEquals(array(
            array('15101',"950  ","9500000","ﾆｲｶﾞﾀｹﾝ","ﾆｲｶﾞﾀｼｷﾀｸ","ｲｶﾆｹｲｻｲｶﾞﾅｲﾊﾞｱｲ","新潟県","新潟市北区","以下に掲載がない場合",'0','0','0','1','0','0'),
            array('15101',"95033","9503315","ﾆｲｶﾞﾀｹﾝ","ﾆｲｶﾞﾀｼｷﾀｸ","ｱｻﾋﾏﾁ","新潟県","新潟市北区","朝日町",'0','0','1','0','0','0'),
            array('15101',"95033","9503377","ﾆｲｶﾞﾀｹﾝ","ﾆｲｶﾞﾀｼｷﾀｸ","ｱﾔﾉ","新潟県","新潟市北区","彩野",'0','0','0','0','0','0')
        ), $result);
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

    public function testCsvExportedFromExcel()
    {
        $parser = \KzykHys\CsvParser\CsvParser::fromFile(__DIR__.'/Resources/csv/excel.csv');
        $result = $parser->parse();

        $this->assertCount(4, $result);
        $this->assertEquals(array(
            "2013/5/1",
            "1.23131E+20",
            'The string contains double quote "',
            '" The string contains double quote',
            'The string contains " double quote',
            "The string contains line breaks\nThe string contains line breaks"
        ), $result[0]);
    }

}