<?php
/**
 * Copyright © tons of limes <https://github.com/tons-of-limes>. All rights reserved.
 */
declare(strict_types=1);

namespace TonsOfLimes\AdminGridAI\Config;

use Magento\Framework\App\Config\ScopeConfigInterface;

/**
 * General configuration of the extension.
 */
class GeneralConfig
{
    private const XML_PATH_IS_ENABLED = 'tonsoflimes_admingridai/general/is_enabled';

    public function __construct(private readonly ScopeConfigInterface $scopeConfig)
    {}

    /**
     * Is the extension enabled.
     *
     * @return bool
     */
    public function isEnabled(): bool
    {
        return $this->scopeConfig->isSetFlag(self::XML_PATH_IS_ENABLED);
    }
}
