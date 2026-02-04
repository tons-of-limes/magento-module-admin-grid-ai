<?php
/**
 * Copyright © tons of limes <https://github.com/tons-of-limes>. All rights reserved.
 */
declare(strict_types=1);

namespace TonsOfLimes\AdminGridAI\Model\Prompt;

use Magento\Framework\Exception\NoSuchEntityException;
use TonsOfLimes\AdminGridAI\Model\Prompt\Data\Prompt;
use TonsOfLimes\AdminGridAI\Model\Prompt\Data\PromptInterface;

/**
 * Registry for managing prompts.
 */
class PromptRegistry implements PromptRegistryInterface
{
    /**
     * @param FileReader $reader
     * @param array $definitions Array of prompt definitions with 'templatePath' and 'variablesDefinitions' keys
     */
    public function __construct(
        private readonly FileReader $reader,
        private readonly array $definitions = []
    ) {}

    /**
     * @inheritDoc
     */
    public function getByCode(string $code): PromptInterface
    {
        if (!isset($this->definitions[$code])) {
            throw new NoSuchEntityException(__('Prompt with code "%1" does not exist.', $code));
        }

        $definition = $this->definitions[$code];
        $template = $this->reader->read($definition['templatePath']);

        return new Prompt(
            $code,
            $template,
            $definition['variablesDefinitions'] ?? []
        );
    }
}

