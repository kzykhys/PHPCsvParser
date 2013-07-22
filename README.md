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
* Double quote in the cell

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

see [CHANGELOG](CHANGELOG.md)

License
-------

[The MIT License](LICENSE)