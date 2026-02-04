<?php
/**
 * Copyright © tons of limes <https://github.com/tons-of-limes>. All rights reserved.
 */
declare(strict_types=1);

namespace TonsOfLimes\AdminGridAI\Model\Prompt\Data;

/**
 * Interface of prompt.
 */
interface PromptInterface
{
    /**
     * Get prompt code.
     *
     * Code is unique identifier of the prompts which can be used for the same purposes.
     *
     * @return string
     */
    public function getCode(): string;

    /**
     * Get prompt template.
     *
     * Can be static text or `twig` template.
     *
     * @return string
     */
    public function getTemplate(): string;

    /**
     * Get list of available dynamic variables for the prompt.
     *
     * @return string[]
     */
    public function getVariablesDefinitions(): array;
}
