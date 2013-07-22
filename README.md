PHPCsvParser
============

Convert CSV to array/Iterator (Excel style is fully suppoted!)

[![Latest Stable Version](https://poser.pugx.org/kzykhys/php-csv-parser/v/stable.png)](https://packagist.org/packages/kzykhys/php-csv-parser)
[![Build Status](https://travis-ci.org/kzykhys/PHPCsvParser.png?branch=master)](https://travis-ci.org/kzykhys/PHPCsvParser)
[![Coverage Status](https://coveralls.io/repos/kzykhys/PHPCsvParser/badge.png)](https://coveralls.io/r/kzykhys/PHPCsvParser)

Why PHPCsvParser?
-----------------

As you know, PHP has built-in `fgetcsv` function.
But has some probrems:

* Line breaks in the cell
* Multibyte string (especially NON UTF-8)

Requirements
------------

**PHP5.3.3 or later**

Installation
------------

Create or modify your composer.json

``` json
{
    "require": {
        "kzykhys/php-csv-parser": "~1.1.0"
    }
}
```

And run

``` sh
$ php composer.phar install
```

Usage
-----

### Parse a CSV file

```
1,"some text",150
2,"some multi line
text",2000
```

``` php
<?php

require('./vendor/autoload.php');

$parser = \KzykHys\CsvParser\CsvParser::fromFile('./test.csv');
$result = $parser->parse();

var_dump($result);
```

This is the same as:

``` php
<?php

require('./vendor/autoload.php');

$iterator = new \KzykHys\CsvParser\Iterator\FileIterator('./test.csv');
$parser = new \KzykHys\CsvParser\CsvParser($iterator);
$result = $parser->parse();

var_dump($result);
```

### Parse from string

``` php
<?php

require('./vendor/autoload.php');

$parser = \KzykHys\CsvParser\CsvParser::fromString($string);
$result = $parser->parse();

var_dump($result);
```

### Parse from array/Iterator

``` php
<?php

require('./vendor/autoload.php');

$parser = \KzykHys\CsvParser\CsvParser::fromArray(array('a,b,c,d', 'e,f,g,h'));
$result = $parser->parse();

$iterator = new ArrayIterator(array('a,b,c,d', 'e,f,g,h'));
$parser2 = new \KzykHys\CsvParser\CsvParser($iterator);
$result2 = $parser2->parse();

var_dump($result);
var_dump($result2);
```

### Handling Large files

The class `\KzykHys\CsvParser\CsvParser` itself is `Traversable`.
so You can convert CSV lines on-the-fly.

Following example is the best choice for performance:

``` php
<?php

require('./vendor/autoload.php');

$parser = \KzykHys\CsvParser\CsvParser::fromFile('./test.csv');

foreach ($parser as $record) {
    // handles each record
    var_dump($record);
}
```

Author
------
Kazuyuki Hayashi (@kzykhys)

Changelog
---------

**1.2.1**:

* Removed internal class `FileIterator`. Use built-in `\SplFileObject` instead.
* Fixed bugs
    * The last line of csv file parsed twice.
    * Passing empty string to CsvParser::fromString() causes infinite loop.

**1.2.0**:

* Added new class CsvIterator
* \IteratorAggregate support for CsvParser

**1.1.0**:

* 1st argument of CsvParser::__constructor is now \Iterator

License
-------

Copyright (c) 2013 Kazuyuki Hayashi

Permission is hereby granted, free of charge, to any person obtaining a copy of
this software and associated documentation files (the "Software"), to deal in
the Software without restriction, including without limitation the rights to use,
copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the
Software, and to permit persons to whom the Software is furnished to do so,
subject to the following conditions:

The above copyright notice and this permission notice shall be included in all
copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS
FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR
COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER
IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN
CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
