<?php
declare(strict_types=1);

namespace PrinsFrank\PdfParser\Tests\Feature;

use PHPUnit\Framework\TestCase;
use PrinsFrank\PdfParser\Document\Text\TextParser;
use PrinsFrank\PdfParser\Document\Version\Version;
use PrinsFrank\PdfParser\Exception\PdfParserException;
use PrinsFrank\PdfParser\PdfParser;

/**
 * @coversNothing
 */
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
        static::assertCount(0, $parsedDocument->errorCollection);
        static::assertCount(2, $parsedDocument->pageCollection);
        var_dump($parsedDocument->pageCollection);
    }

    /**
     * @dataProvider pdfs
     * @throws PdfParserException
     */
    public function testExternalSourcePDFs(string $pdfPath): void
    {
        $parser = new PdfParser();

        $document = $parser->parse(file_get_contents($pdfPath));
        static::assertCount(0, $document->errorCollection);
    }

    public function pdfs(): iterable
    {
        $basePath = dirname(__DIR__, 2) . '/_samples/pdf/samples/';
        foreach (array_diff(scandir($basePath), ['.', '..']) as $file) {
            yield $file => [$basePath . $file];
        }
    }

    public function testParseText(): void
    {
        $parsedResult = TextParser::parse('BT
/F20 9.9626 Tf 148.712 707.125 Td [(Sen)28(tence)-334(on)-333(\014rst)-333(page)]TJ 154.421 -567.87 Td [(1)]TJ
ET');
        var_dump($parsedResult);
    }
}
