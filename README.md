PHPCsvParser
============

Convert CSV to array (Excel style is fully suppoted!)

Requirements
------------

**PHP5.3.3 or later**

Installation
------------

Create or modify your composer.json

```
{
    "require": {
        "kzykhys/php-csv-parser": "1.0.x"
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

Author
------
Kazuyuki Hayashi (@kzykhys)

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