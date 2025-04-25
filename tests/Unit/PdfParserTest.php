<?php declare(strict_types=1);

namespace PrinsFrank\PdfParser\Tests\Unit;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use PrinsFrank\PdfParser\Exception\InvalidArgumentException;
use PrinsFrank\PdfParser\PdfParser;

#[CoversClass(PdfParser::class)]
class PdfParserTest extends TestCase {
    public function testParseStringInMemory(): void {
        static::assertNotFalse($fileContent = file_get_contents(dirname(__DIR__) . '/Feature/samples/h3-simple-string.pdf'));
        static::assertSame(
            'Hello World',
            (new PdfParser())
                ->parseString(
                    $fileContent
                )->getText(),
        );
    }

    public function testParseStringWithFileCache(): void {
        static::assertNotFalse($fileContent = file_get_contents(dirname(__DIR__) . '/Feature/samples/h3-simple-string.pdf'));
        static::assertSame(
            'Hello World',
            (new PdfParser())
                ->parseString(
                    $fileContent,
                    true,
                )->getText(),
        );
    }

    public function testParseFileInMemory(): void {
        static::assertSame(
            'Hello World',
            (new PdfParser())
                ->parseFile(
                    dirname(__DIR__) . '/Feature/samples/h3-simple-string.pdf'
                )
                ->getText()
        );
    }

    public function testParseFileWithoutMemoryStream(): void {
        static::assertSame(
            'Hello World',
            (new PdfParser())
                ->parseFile(
                    dirname(__DIR__) . '/Feature/samples/h3-simple-string.pdf',
                    false
                )
                ->getText()
        );
    }

    public function testParseFileThrowsExceptionWhenUnableToOpenFile(): void {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Failed to open file at path "oeuoeu"');
        (new PdfParser())->parseFile('oeuoeu');
    }

    /**
      * @see https://github.com/PrinsFrank/pdfparser/issues/40
      */
      public function testIssue40(): void {
        /*
         * Running the following code should lead to the exception:
         *
         *      TypeError: array_slice(): Argument #3 ($length) must be of type ?int, string given
         *
         * Stack trace:
         *
         * 1) PrinsFrank\PdfParser\Tests\Unit\PdfParserTest::testIssue40
         *
         * /var/www/html/src/Document/CrossReference/Stream/CrossReferenceStreamParser.php:73
         * /var/www/html/src/Document/CrossReference/CrossReferenceSourceParser.php:48
         * /var/www/html/src/PdfParser.php:22
         * /var/www/html/src/PdfParser.php:42
         * /var/www/html/tests/Unit/PdfParserTest.php:81
         */

        $parser = new PdfParser();
        static::assertSame(
            '',
            $parser
                ->parseFile(__DIR__ . '/mbl-2025-198.pdf')
                ->getText(PHP_EOL)
        );
    }
}
