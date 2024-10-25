<?php
declare(strict_types=1);

namespace PrinsFrank\PdfParser\Document\Page;

use Countable;

class PageCollection implements Countable {
    /** @var list<Page> */
    public readonly array $pages;

    public function __construct(
        Page ...$pages
    ) {
        $this->pages = $pages;
    }

    public function count(): int {
        return count($this->pages);
    }
}
