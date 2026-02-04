<?php
/**
 * Copyright © tons of limes <https://github.com/tons-of-limes>. All rights reserved.
 */
declare(strict_types=1);

namespace TonsOfLimes\AdminGridAI\Model\LLM;

use OpenAI;
use OpenAI\Client;
use TonsOfLimes\AdminGridAI\Config\GatewayConfig;
use TonsOfLimes\AdminGridAI\Model\LLM\Exception\GatewayConfigException;

/**
 * Factory to create LLM Gateway clients based on configuration.
 */
class GatewayClientFactory
{
    public function __construct(
        private readonly GatewayConfig $gatewayConfig,
    ) {}

    /**
     * @throws GatewayConfigException
     */
    public function create(): Client
    {
        $type = $this->gatewayConfig->getType();

        return match ($type) {
            'openai' => $this->createOpenAIClient(),
            'openrouter' => $this->createOpenRouterClient(),
            'custom' => $this->createCustomGatewayClient(),
            default => throw new GatewayConfigException(__('Invalid LLM Gateway configuration type: %1', $type)),
        };
    }

    /**
     * Create OpenAI client.
     *
     * @return Client
     */
    private function createOpenAIClient(): Client
    {
        return OpenAI::factory()
            ->withApiKey($this->gatewayConfig->getOpenAIAPIKey())
            ->make();
    }

    /**
     * Create OpenRouter client.
     *
     * @return Client
     */
    private function createOpenRouterClient(): Client
    {
        return OpenAI::factory()
            ->withBaseUri($this->gatewayConfig->getOpenRouterBaseURI())
            ->withApiKey($this->gatewayConfig->getOpenRouterAPIKey())
            ->make();
    }


    /**
     * Create Custom Gateway client.
     *
     * @return Client
     */
    private function createCustomGatewayClient(): Client
    {
        return OpenAI::factory()
            ->withBaseUri($this->gatewayConfig->getCustomGatewayBaseURI())
            ->withApiKey($this->gatewayConfig->getCustomGatewayAPIKey())
            ->make();
    }
}
