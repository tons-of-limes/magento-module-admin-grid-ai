<?php
/**
 * Copyright © tons of limes <https://github.com/tons-of-limes>. All rights reserved.
 */
declare(strict_types=1);

namespace TonsOfLimes\AdminGridAI\Model\Ui\Data;

class ListingStructure
{
    public function __construct(
        private readonly string $name,
        private readonly array $fields,
        private readonly bool $isMultiSelectEnabled
    ) {}

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return Field[]
     */
    public function getFields(): array
    {
        return $this->fields;
    }

    /**
     * @return bool
     */
    public function isMultiSelectEnabled(): bool
    {
        return $this->isMultiSelectEnabled;
    }
}
