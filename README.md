<picture>
    <source srcset="https://github.com/PrinsFrank/pdfparser/raw/main/docs/images/banner_dark.png" media="(prefers-color-scheme: dark)">
    <img src="https://github.com/PrinsFrank/pdfparser/raw/main/docs/images/banner_light.png" alt="Banner">
</picture>

# PDF Parser

[![GitHub](https://img.shields.io/github/license/prinsfrank/pdfparser)](https://github.com/PrinsFrank/pdfparser/blob/main/LICENSE)
[![PHP Version Support](https://img.shields.io/packagist/php-v/prinsfrank/pdfparser)](https://github.com/PrinsFrank/pdfparser/blob/main/composer.json)
[![codecov](https://codecov.io/gh/PrinsFrank/pdfparser/branch/main/graph/badge.svg?token=2KXO43MCIC)](https://codecov.io/gh/PrinsFrank/pdfparser)
[![PHPStan Level](https://img.shields.io/badge/PHPStan-level%209-brightgreen.svg?style=flat)](https://github.com/PrinsFrank/pdfparser/blob/main/phpstan.neon)

A low-memory, fast and maintainable conforming PDF Parser

> :mega: [Call for testers](https://github.com/PrinsFrank/pdf-samples)

## Why this library?

Previously, there wasn't a PDF library that allows parsing of PDFs that was open source, MIT licensed and under active development. The PDFParser by smalot, while having been very useful over the years isn't under active development anymore. The parser of Setasign is not MIT licensed and not open source. And several other packages rely on java/js/python dependencies being installed that are called behind by php the scenes, losing any type information and underlying structure.

Instead, this package allows for parsing of a wide variety of PDF files while not relying on external dependencies, all while being MIT licensed!

## Setup

To start right away, run the following command in your composer project;

```bash
composer require prinsfrank/pdfparser
```
