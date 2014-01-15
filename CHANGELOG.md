CHANGELOG
=========

**1.4.1**:

* Fixed: #4 #5

**1.4.0**:

* Reverted internal class `FileIterator`.
* Fixed #2 last line of csv file is empty

**1.3.0**:

* Added new options: `offset`, `limit`, `header`

**1.2.2**:

* Fixed bug: The escaped double quote at beginning or end of string will be dropped.

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