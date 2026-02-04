<?php
/**
 * Copyright © tons of limes <https://github.com/tons-of-limes>. All rights reserved.
 */
declare(strict_types=1);

namespace TonsOfLimes\AdminGridAI\Model\Prompt;

use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\NoSuchEntityException;
use Twig\Environment;
use Twig\Error\Error;
use Twig\Loader\ArrayLoader;

/**
 * Service to get prompts by code and prepare them with variables.
 */
class GetPrompt
{
    public function __construct(
        private readonly PromptRegistryInterface $promptRegistry
    ) {}

    /**
     * Get prompt by code and prepare it with variables.
     *
     * @param string $code
     * @param array $variables
     * @return string
     * @throws InputException
     * @throws NoSuchEntityException
     */
    public function execute(string $code, array $variables = []): string
    {
        $prompt = $this->promptRegistry->getByCode($code);

        $twig = new Environment(new ArrayLoader([
            'prompt' => $prompt->getTemplate(),
        ]));

        try {
            $promptString = $twig->render('prompt', ['vars' => $variables]);
        } catch (Error $error) {
            throw new InputException(__('Error rendering prompt template: %1', $error->getMessage()));
        }

        return $promptString;
    }
}
