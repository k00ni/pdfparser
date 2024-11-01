<?php
declare(strict_types=1);

namespace PrinsFrank\PdfParser\Tests\Feature;

use PHPUnit\Framework\Attributes\CoversNothing;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use PrinsFrank\PdfParser\Exception\PdfParserException;
use PrinsFrank\PdfParser\Stream;
use PrinsFrank\PdfParser\PdfParser;

#[CoversNothing]
class ParsedResultTest extends TestCase {
    /** @throws PdfParserException */
    #[DataProvider('externalSamples')]
    public function testExternalSourcePDFs(string $pdfPath): void {
        $parser = new PdfParser();

        $parser->parse(Stream::openFile($pdfPath));
        $this->addToAssertionCount(1);
    }

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
