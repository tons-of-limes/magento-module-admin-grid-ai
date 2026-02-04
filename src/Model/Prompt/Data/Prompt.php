<?php
/**
 * Copyright © tons of limes <https://github.com/tons-of-limes>. All rights reserved.
 */
declare(strict_types=1);

namespace TonsOfLimes\AdminGridAI\Model\Prompt\Data;

/**
 * Implementation of prompt.
 */
readonly class Prompt implements PromptInterface
{
    public function __construct(
        private string $code,
        private string $template,
        private array  $variablesDefinitions
    ) {}

    /**
     * @inheritDoc
     */
    public function getCode(): string
    {
        return $this->code;
    }

    /**
     * @inheritDoc
     */
    public function getTemplate(): string
    {
        return $this->template;
    }

    /**
     * @inheritDoc
     */
    public function getVariablesDefinitions(): array
    {
        return $this->variablesDefinitions;
    }
}
