<?php
declare(strict_types=1);

namespace PrinsFrank\PdfParser\Document\Object;

class ObjectItemCollection
{
    /** @var array<ObjectItem> */
    private array $objectItems = [];

    public function addObjectItem(ObjectItem $objectItem): self
    {
        $this->objectItems[] = $objectItem;

        return $this;
    }

    /** @return array<ObjectItem> */
    public function getObjectItems(): array
    {
        return $this->objectItems;
    }
}
