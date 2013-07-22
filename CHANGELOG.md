CHANGELOG
=========

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