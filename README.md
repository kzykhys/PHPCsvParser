PHPCsvParser
============

> This is a fork of [kzykhys/PHPCsvParser](https://github.com/kzykhys/PHPCsvParser).
> 
> The only purpose of this fork is to fully support PHP 8 and fix some deprecation warnings.
> 
> All of the core functionality remains the same.

Convert CSV to array/Iterator (Excel style is fully supported!)

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
        "kzykhys/php-csv-parser": ">1.4"
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

$iterator = new \SplFileObject('./test.csv');
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

### Options

You can pass the options to 2nd argument of each static methods.

* CsvParser::fromFile($file, **$options**);
* CsvParser::fromString($string, **$options**);
* CsvParser::fromArray($array, **$options**);
* new CsvParser($iterator, **$options**);

Available options are:

| Option     | Type           | Description                                           | Default        |
| ---------- |--------------- | ----------------------------------------------------- | -------------- |
| delimiter  | string         | The field delimiter (one character only)              | ,              |
| enclosure  | string         | The field enclosure character (one character only)    | "              |
| encoding   | string         | The type of encoding                                  | CP932          |
| offset     | integer (>=0)  | The sequence will start at that offset                | 0              |
| limit      | integer (>=-1) | Limit maximum count of records                        | -1 (unlimited) |
| header     | array or false | Use the specified index instead of the column number  | false          |

Testing
-------

Just run `phpunit` (PHPUnit is required)

Author
------
Kazuyuki Hayashi (@kzykhys)

Changelog
---------

see [CHANGELOG](CHANGELOG.md)

License
-------

[The MIT License](LICENSE)
