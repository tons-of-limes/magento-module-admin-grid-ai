<?php
/**
 * Copyright © tons of limes <https://github.com/tons-of-limes>. All rights reserved.
 */
declare(strict_types=1);

namespace TonsOfLimes\AdminGridAI\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;

class GatewayType implements OptionSourceInterface
{
    /**
     * @inheritdoc
     */
    public function toOptionArray(): array
    {
        return [
            ['value' => 'openai', 'label' => __('OpenAI')],
            ['value' => 'openrouter', 'label' => __('OpenRouter')],
            ['value' => 'custom', 'label' => __('Custom OpenAI-compatible gateway')],
        ];
    }
}
