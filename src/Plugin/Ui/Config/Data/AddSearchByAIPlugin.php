<?php
/**
 * Copyright © tons of limes <https://github.com/tons-of-limes>. All rights reserved.
 */
declare(strict_types=1);

namespace TonsOfLimes\AdminGridAI\Plugin\Ui\Config\Data;

use Magento\Framework\UrlInterface;
use Magento\Ui\Component\Listing;
use Magento\Ui\Config\Data;
use TonsOfLimes\AdminGridAI\Config\GeneralConfig;
use TonsOfLimes\AdminGridAI\Ui\Component\ContainerDefinitionBuilder;

/**
 * Add Search By AI component to listing configuration.
 */
class AddSearchByAIPlugin
{
    public function __construct(
        private readonly GeneralConfig $generalConfig,
        private readonly UrlInterface $url,
        private readonly ContainerDefinitionBuilder $containerDefinitionBuilder
    ) {}

    /**
     * @param Data $subject
     * @param $result
     * @param $path
     * @return array|mixed
     */
    public function afterGet(
        Data $subject,
        $result,
        $path = null
    ) {
        if (!$this->generalConfig->isEnabled()) {
            return $result;
        }

        if (!is_array($result) || !is_string($path)) {
            return $result;
        }

        if (!$this->isListingComponentConfig($result)) {
            return $result;
        }

        if (!isset($result['children']['listing_top']['children'])) {
            return $result;
        }

        $componentName = 'search-by-ai';
        $definition = $this->buildSearchByAIComponentDefinition(
            $componentName,
            $path
        );

        $componentActionName = 'search-by-ai-action';

        return array_replace_recursive(
            $result,
            [
                'children' => [
                    'listing_top' => [
                        'children' => [
                            $componentActionName => $this->containerDefinitionBuilder->buildByConfig(
                                $componentActionName,
                                [
                                    'component' => 'TonsOfLimes_AdminGridAI/js/grid/actions/ai',
                                    'displayArea' => 'dataGridActions',
                                ]
                            ),
                            $componentName => $definition,
                        ],
                    ],
                ]
            ]
        );
    }

    /**
     * Build definition of search by AI component.
     *
     * @param string $componentName
     * @param string $listingName
     * @return array
     */
    private function buildSearchByAIComponentDefinition(
        string $componentName,
        string $listingName
    ): array {
        $buildStateUrl = $this->url->getUrl('tonsoflimes_admingridai/grid/buildStateByQuery', [
            'namespace' => $listingName,
        ]);

        return $this->containerDefinitionBuilder->buildByConfig($componentName, [
            'component' => 'TonsOfLimes_AdminGridAI/js/grid/ai',
            'regionSortOrder' => '1000',
            'displayArea' => 'dataGridFilters',
            'buildStateUrlByQuery' => $buildStateUrl,
        ]);
    }

    /**
     * Is config definition of listing component.
     *
     * @param array $config
     * @return bool
     */
    private function isListingComponentConfig(array $config): bool
    {
        $rootComponentClass = $config['attributes']['class'] ?? null;

        return $rootComponentClass && $this->isClassOrSubclass($rootComponentClass, Listing::class);
    }

    /**
     * Is class or subclass of the class.
     *
     * @param string $class
     * @param string $parentClass
     * @return bool
     */
    private function isClassOrSubclass(string $class, string $parentClass): bool
    {
        return $class === $parentClass || is_subclass_of($class, $parentClass);
    }
}
