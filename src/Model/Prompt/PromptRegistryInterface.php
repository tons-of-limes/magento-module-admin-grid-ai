<?php
/**
 * Copyright © tons of limes <https://github.com/tons-of-limes>. All rights reserved.
 */
declare(strict_types=1);

namespace TonsOfLimes\AdminGridAI\Model\Prompt;

use Magento\Framework\Exception\NoSuchEntityException;
use TonsOfLimes\AdminGridAI\Model\Prompt\Data\PromptInterface;

interface PromptRegistryInterface
{
    /**
     * Get prompt by code.
     *
     * @param string $code
     * @return PromptInterface
     * @throws NoSuchEntityException
     */
    public function getByCode(string $code): PromptInterface;
}
