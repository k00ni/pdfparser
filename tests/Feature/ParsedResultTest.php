<?php
declare(strict_types=1);

namespace PrinsFrank\PdfParser\Tests\Feature;

use PHPUnit\Framework\Attributes\CoversNothing;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use PrinsFrank\PdfParser\Stream;
use PrinsFrank\PdfParser\PdfParser;

#[CoversNothing]
class ParsedResultTest extends TestCase {
    #[DataProvider('externalSamples')]
    public function testExternalSourcePDFs(string $pdfPath): void {
        $parser = new PdfParser();

        $document = $parser->parse(Stream::openFile($pdfPath));
        $document->getCatalog();
        $document->getInformationDictionary();
        $this->addToAssertionCount(1);
    }

    /** @return iterable<string, array{0: string}> */
    public static function externalSamples(): iterable {
        $files = scandir($basePath = __DIR__ . '/samples/external/');
        if ($files === false) {
            return;
        }

        foreach (array_diff($files, ['.', '..', '.gitkeep']) as $file) {
            yield $file => [$basePath . $file];
        }
    }
}
