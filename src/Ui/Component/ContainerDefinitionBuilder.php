<?php
/**
 * Copyright © tons of limes <https://github.com/tons-of-limes>. All rights reserved.
 */
declare(strict_types=1);

namespace TonsOfLimes\AdminGridAI\Ui\Component;

/**
 * Build of container component definition.
 */
class ContainerDefinitionBuilder
{
    /**
     * Build component definition by config.
     *
     * @param string $name
     * @param array $config
     * @return array
     */
    public function buildByConfig(string $name, array $config): array
    {
        return [
            'arguments' => [
                'data' => [
                    'config' => $config,
                ],
            ],
            'attributes' => [
                'class' => 'Magento\\Ui\\Component\\Container',
                'name' => $name,
            ],
            'children' => [],
        ];
    }
}
