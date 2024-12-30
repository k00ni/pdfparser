<picture>
    <source srcset="https://github.com/PrinsFrank/pdfparser/raw/main/docs/images/banner_dark.png" media="(prefers-color-scheme: dark)">
    <img src="https://github.com/PrinsFrank/pdfparser/raw/main/docs/images/banner_light.png" alt="Banner">
</picture>

# PDF Parser

[![GitHub](https://img.shields.io/github/license/prinsfrank/pdfparser)](https://github.com/PrinsFrank/pdfparser/blob/main/LICENSE)
[![PHP Version Support](https://img.shields.io/packagist/php-v/prinsfrank/pdfparser)](https://github.com/PrinsFrank/pdfparser/blob/main/composer.json)
[![codecov](https://codecov.io/gh/PrinsFrank/pdfparser/branch/main/graph/badge.svg?token=2KXO43MCIC)](https://codecov.io/gh/PrinsFrank/pdfparser)
[![PHPStan Level](https://img.shields.io/badge/PHPStan-level%209-brightgreen.svg?style=flat)](https://github.com/PrinsFrank/pdfparser/blob/main/phpstan.neon)
[![](https://img.shields.io/static/v1?label=Sponsor&message=%E2%9D%A4&logo=GitHub&color=%23fe8e86)](https://github.com/sponsors/PrinsFrank)

A low-memory, fast and maintainable conforming PDF Parser

> :mega: [Call for testers](https://github.com/PrinsFrank/pdfparser/issues/2)

## Why this library?

Previously, there wasn't a PDF library that allows parsing of PDFs that was open source, MIT licensed and under active development. The PDFParser by smalot, while having been very useful over the years isn't under active development anymore. The parser of Setasign is not MIT licensed and not open source. And several other packages rely on java/js/python dependencies being installed that are called by PHP behind the scenes, losing any type information and underlying structure.

Instead, this package allows for parsing of a wide variety of PDF files while not relying on external dependencies, all while being MIT licensed!

## Setup

To start right away, run the following command in your composer project;

```bash
composer require prinsfrank/pdfparser
```

## Opening a PDF

To open a PDF file, you'll first need to load it and retrieve a "Document" object. That can be done by either parsing a file directly, or parsing a PDF from a string variable.

### Parsing a PDF file

Parsing a PDF from a file directly is the easiest option and also uses the least amount of memory. To do so, simply call the `parseFile` method on a `PdfParser` instance:

```php
use PrinsFrank\PdfParser;

$document = (new PdfParser())
    ->parseFile(dirname(__DIR__, 3) . '/path/to/file.pdf');
```

### Parsing PDF from string

It is also possible to parse a PDF from a string in a variable. To do so, pass the string as an argument for the `parseFile` method on a `PdfParser` instance. This has a bigger memory footprint while loading the file into memory, but the file will be written to a temp file while processing.

```php
use PrinsFrank\PdfParser;

$pdfAsString = file_get_contents(dirname(__DIR__, 3) . '/path/to/file.pdf');

$document = (new PdfParser())
    ->parseFile($pdfAsString);
```
