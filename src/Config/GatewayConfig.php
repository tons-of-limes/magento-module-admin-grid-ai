<?php
/**
 * Copyright © tons of limes <https://github.com/tons-of-limes>. All rights reserved.
 */
declare(strict_types=1);

namespace TonsOfLimes\AdminGridAI\Config;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Encryption\EncryptorInterface;

/**
 * LLM Gateway configuration of the extension.
 */
class GatewayConfig
{
    private const XML_PATH_TYPE = 'tonsoflimes_admingridai/gateway/type';

    private const XML_PATH_OPENAI_API_KEY = 'tonsoflimes_admingridai/gateway/openai_api_key';

    private const XML_PATH_OPENROUTER_BASE_URI = 'tonsoflimes_admingridai/gateway/openrouter_base_uri';
    private const XML_PATH_OPENROUTER_API_KEY = 'tonsoflimes_admingridai/gateway/openrouter_api_key';

    private const XML_PATH_CUSTOM_BASE_URI = 'tonsoflimes_admingridai/gateway/custom_base_uri';
    private const XML_PATH_CUSTOM_API_KEY = 'tonsoflimes_admingridai/gateway/custom_api_key';

    public function __construct(
        private readonly ScopeConfigInterface $scopeConfig,
        private readonly EncryptorInterface $encryptor
    ) {}

    /**
     * Get type.
     *
     * @return string|null
     */
    public function getType(): string|null
    {
        return $this->scopeConfig->getValue(self::XML_PATH_TYPE);
    }

    /**
     * Get OpenAI API key.
     *
     * @return string|null
     */
    public function getOpenAIAPIKey(): string|null
    {
        return $this->getNullableEncryptedString(self::XML_PATH_OPENAI_API_KEY);
    }

    /**
     * Get OpenRouter Base URI.
     *
     * @return string|null
     */
    public function getOpenRouterBaseURI(): string|null
    {
        return $this->scopeConfig->getValue(self::XML_PATH_OPENROUTER_BASE_URI);
    }

    /**
     * Get OpenRouter API key.
     *
     * @return string|null
     */
    public function getOpenRouterAPIKey(): string|null
    {
        return $this->getNullableEncryptedString(self::XML_PATH_OPENROUTER_API_KEY);
    }

    /**
     * Get Custom Gateway Base URI.
     *
     * @return string|null
     */
    public function getCustomGatewayBaseURI(): string|null
    {
        return $this->scopeConfig->getValue(self::XML_PATH_CUSTOM_BASE_URI);
    }

    /**
     * Get Custom Gateway API key.
     *
     * @return string|null
     */
    public function getCustomGatewayAPIKey(): string|null
    {
        return $this->getNullableEncryptedString(self::XML_PATH_CUSTOM_API_KEY);
    }

    /**
     * Get nullable encrypted string from config.
     *
     * @param string $path
     * @param string $scopeType
     * @param string|int|null $scopeCode
     * @return string|null
     */
    private function getNullableEncryptedString(
        string $path,
        string $scopeType = ScopeConfigInterface::SCOPE_TYPE_DEFAULT,
        string|int $scopeCode = null
    ): string|null {
        $value = $this->scopeConfig->getValue($path, $scopeType, $scopeCode);

        if ($value === null) {
            return null;
        }

        return $this->encryptor->decrypt((string)$value);
    }
}
