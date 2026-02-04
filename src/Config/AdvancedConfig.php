<?php
/**
 * Copyright © tons of limes <https://github.com/tons-of-limes>. All rights reserved.
 */
declare(strict_types=1);

namespace TonsOfLimes\AdminGridAI\Config;

use Magento\Framework\App\Config\ScopeConfigInterface;

/**
 * Advanced configuration of the extension.
 */
class AdvancedConfig
{
    private const XML_PATH_MAX_OPTIONS_SIZE = 'tonsoflimes_admingridai/advanced/max_options_size';

    public function __construct(private readonly ScopeConfigInterface $scopeConfig)
    {}

    /**
     * Max select options size in listing structure. Zero disables limiting.
     *
     * @return int
     */
    public function getMaxOptionsSize(): int
    {
        $value = (int)$this->scopeConfig->getValue(self::XML_PATH_MAX_OPTIONS_SIZE);

        return $value > 0 ? $value : 0;
    }
}
