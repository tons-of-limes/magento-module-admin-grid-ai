<?php
/**
 * Copyright © tons of limes <https://github.com/tons-of-limes>. All rights reserved.
 */
declare(strict_types=1);

namespace TonsOfLimes\AdminGridAI\Model\Ui\Data;

class Column
{
    public function __construct(
        private readonly bool $isSortable,
        private readonly bool $isVisible,
    ) {}

    public function isVisible(): bool
    {
        return $this->isVisible;
    }

    public function isSortable(): bool
    {
        return $this->isSortable;
    }
}
