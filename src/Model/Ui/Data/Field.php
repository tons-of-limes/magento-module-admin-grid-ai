<?php
/**
 * Copyright © tons of limes <https://github.com/tons-of-limes>. All rights reserved.
 */
declare(strict_types=1);

namespace TonsOfLimes\AdminGridAI\Model\Ui\Data;

class Field
{
    public function __construct(
        private readonly string $name,
        private readonly string $label,
        private readonly ?Column $column,
        private readonly ?Filter $filter
    ) {}

    public function getName(): string
    {
        return $this->name;
    }

    public function getLabel(): string
    {
        return $this->label;
    }

    public function getColumn(): ?Column
    {
        return $this->column;
    }

    public function getFilter(): ?Filter
    {
        return $this->filter;
    }
}
