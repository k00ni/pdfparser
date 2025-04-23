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

    /**
     * @see https://github.com/PrinsFrank/pdfparser/issues/43
     */
    public function testIssue43(): void {
        /*
         * Running the following code should lead to the exception:
         *
         *      PrinsFrank\PdfParser\Exception\ParseFailureException: Value "/Viewport" for dictionary key Type could not be parsed to a valid value type
         *
         * Stack trace:
         *
         * /var/www/html/src/Document/Dictionary/DictionaryEntry/DictionaryEntryFactory.php:75
         * /var/www/html/src/Document/Dictionary/DictionaryEntry/DictionaryEntryFactory.php:29
         * /var/www/html/src/Document/Dictionary/DictionaryFactory.php:24
         * /var/www/html/src/Document/Dictionary/DictionaryParser.php:83
         * /var/www/html/src/Document/Object/Item/UncompressedObject/UncompressedObject.php:49
         * /var/www/html/src/Document/Object/Decorator/DecoratedObjectFactory.php:22
         * /var/www/html/src/Document/Document.php:112
         * /var/www/html/src/Document/Object/Decorator/Pages.php:19
         * /var/www/html/src/Document/Document.php:132
         * /var/www/html/src/Document/Document.php:141
         * /var/www/html/tests/Unit/PdfParserTest.php:80
         */

        $parser = new PdfParser();
        static::assertSame(
            '',
            $parser
                ->parseFile(__DIR__ . '/mbl-2025-16.pdf')
                ->getText(PHP_EOL)
        );
    }

    public function testParseFileThrowsExceptionWhenUnableToOpenFile(): void {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Failed to open file at path "oeuoeu"');
        (new PdfParser())->parseFile('oeuoeu');
    }
}
