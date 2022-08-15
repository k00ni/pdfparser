<?php
declare(strict_types=1);

namespace PrinsFrank\PdfParser\Tests\Feature;

use PHPUnit\Framework\TestCase;
use PrinsFrank\PdfParser\Document\Version\Version;
use PrinsFrank\PdfParser\Exception\PdfParserException;
use PrinsFrank\PdfParser\PdfParser;

class ParsedResultTest extends TestCase
{
    /**
     * @throws PdfParserException
     */
    public function testSimpleDocument(): void
    {
        $parser = new PdfParser();

        $parsedDocument = $parser->parse(file_get_contents(dirname(__DIR__, 2) . '/_samples/pdf/simple_document.pdf'));
        static::assertEquals(Version::V1_5, $parsedDocument->version);
    }

    /**
     * @dataProvider pdfs
     * @throws PdfParserException
     */
    public function testExternalSourcePDFs(string $pdfPath): void
    {
        $parser = new PdfParser();

        $document = $parser->parse(file_get_contents($pdfPath));
        static::assertSame([], $document->getErrors());
        $this->addToAssertionCount(1);
    }

    public function pdfs(): iterable
    {
        $basePath = dirname(__DIR__, 2) . '/_samples/pdf/samples/';
        foreach (array_diff(scandir($basePath), ['.', '..']) as $file) {
            yield $file => [$basePath . $file];
        }
    }
}
