<?php
declare(strict_types=1);

namespace PrinsFrank\PdfParser\Tests\Feature;

use PHPUnit\Framework\TestCase;
use PrinsFrank\PdfParser\Document\Version\Version;
use PrinsFrank\PdfParser\Exception\PdfParserException;
use PrinsFrank\PdfParser\File;
use PrinsFrank\PdfParser\PdfParser;

/**
 * @coversNothing
 */
class ParsedResultTest extends TestCase {
    /** @throws PdfParserException */
    public function testSimpleDocument(): void {
        $parser = new PdfParser();

        $parsedDocument = $parser->parse(File::open(dirname(__DIR__, 2) . '/_samples/pdf/simple_document.pdf'));
        static::assertEquals(Version::V1_5, $parsedDocument->version);
        static::assertCount(0, $parsedDocument->errorCollection);
        static::assertCount(2, $parsedDocument->pageCollection);
    }

    /** @throws PdfParserException */
    public function testSimpleDocumentWithTitles(): void {
        $parser = new PdfParser();

        $parsedDocument = $parser->parse(File::open(dirname(__DIR__, 2) . '/_samples/pdf/simple_document_with_titles.pdf'));
        static::assertEquals(Version::V1_5, $parsedDocument->version);
        static::assertCount(0, $parsedDocument->errorCollection);
        static::assertCount(2, $parsedDocument->pageCollection);
    }

    /**
     * @dataProvider pdfs
     *
     * @throws PdfParserException
     */
    public function testExternalSourcePDFs(string $pdfPath): void {
        $parser = new PdfParser();

        $document = $parser->parse(File::open($pdfPath));
        static::assertCount(0, $document->errorCollection);
    }

    public static function pdfs(): iterable {
        $basePath = dirname(__DIR__, 2) . '/_samples/pdf/samples/';

        $files = scandir($basePath);
        if ($files === false) {
            return;
        }

        foreach (array_diff($files, ['.', '..', '.gitkeep']) as $file) {
            yield $file => [$basePath . $file];
        }
    }
}
