<?php
/**
 * Copyright © tons of limes <https://github.com/tons-of-limes>. All rights reserved.
 */
declare(strict_types=1);

namespace TonsOfLimes\AdminGridAI\Model\Ui;

use Magento\Ui\Component\Filters;
use TonsOfLimes\AdminGridAI\Model\Ui\Data\Column;
use TonsOfLimes\AdminGridAI\Model\Ui\Data\Field;
use TonsOfLimes\AdminGridAI\Model\Ui\Data\Filter;
use TonsOfLimes\AdminGridAI\Model\Ui\Data\ListingStructure;
use TonsOfLimes\AdminGridAI\Config\AdvancedConfig;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Framework\View\Element\UiComponentInterface;
use Magento\Ui\Component\Filters\Type\AbstractFilter;
use Magento\Ui\Component\Filters\Type\Date;
use Magento\Ui\Component\Filters\Type\Input;
use Magento\Ui\Component\Filters\Type\Range;
use Magento\Ui\Component\Filters\Type\Search;
use Magento\Ui\Component\Filters\Type\Select;
use Magento\Ui\Component\Listing\Columns\Column as UiColumn;

class GetListingComponentStructure
{
    public function __construct(
        private readonly UiComponentFactory $uiComponentFactory,
        private readonly AdvancedConfig $advancedConfig
    ) {}

    public function execute(string $componentName): ListingStructure
    {
        $component = $this->uiComponentFactory->create($componentName);

        $this->prepareComponent($component);

        $fields = $this->buildFields($component);

        return new ListingStructure(
            $componentName,
            $fields,
            $this->isMultiSelectEnabled($component)
        );
    }

    /**
     * Is multiselect enabled for listing filters.
     *
     * @param UiComponentInterface $rootComponent
     * @return bool
     */
    private function isMultiSelectEnabled(UiComponentInterface $rootComponent): bool
    {
        $components = $rootComponent->getContext()->getProcessor()->getComponents();
        $filtersContainerComponent = array_find($components, function (UiComponentInterface $component): bool {
            return $component instanceof Filters;
        });

        if (!$filtersContainerComponent instanceof Filters) {
            return false;
        }

        $configuration = $filtersContainerComponent->getConfiguration();
        $selectFilterJsComponent = $configuration['templates']['filters']['select']['component'] ?? null;

        return $selectFilterJsComponent === 'Magento_Ui/js/form/element/ui-select';
    }

    /**
     * Build listing fields by listing component.
     *
     * @param UiComponentInterface $component
     * @return Field[]
     */
    private function buildFields(UiComponentInterface $component): array
    {
        $components = $component->getContext()->getProcessor()->getComponents();
        $filterComponents = $this->getFilterComponents($components);

        $fields = [];
        foreach ($filterComponents as $filterComponent) {
            $columnComponent = $this->getColumnComponentByName($filterComponent->getName(), $components);

            $fields[$filterComponent->getName()] = $this->buildFieldByFilterComponent(
                $filterComponent,
                $columnComponent
            );
        }

        $columnComponents = $this->getColumnComponents($components);
        $columnsWithoutFilter = array_filter($columnComponents, function ($columnComponent) use ($fields) {
            return !isset($fields[$columnComponent->getName()]);
        });

        foreach ($columnsWithoutFilter as $columnComponent) {
            $fields[$columnComponent->getName()] = $this->buildFieldByColumn(
                $columnComponent
            );
        }

        return array_filter(array_values($fields));
    }

    /**
     * @param array $components
     * @return AbstractFilter[]
     */
    private function getFilterComponents(array $components): array
    {
        return array_filter($components, function ($component) {
            return $component instanceof AbstractFilter;
        });
    }

    /**
     * @param array $components
     * @return UiColumn[]
     */
    private function getColumnComponents(array $components): array
    {
        return array_filter($components, function ($component) {
            return $component instanceof UiColumn;
        });
    }

    /**
     * Get column component by name.
     *
     * @param string $name
     * @param array $components
     * @return UiColumn|null
     */
    private function getColumnComponentByName(string $name, array $components): ?UiColumn
    {
        return array_find(
            $components,
            fn($component) => $component instanceof UiColumn && $component->getName() === $name
        );

    }

    /**
     * @param UiColumn $column
     * @return Field
     */
    private function buildFieldByColumn(UiColumn $column): Field
    {
        return new Field(
            $column->getName(),
            (string)$column->getData('config/label'),
            $this->buildColumnByUiColumn($column),
            null,
        );
    }

    /**
     * Build Field DTO by Filter Ui Component.
     *
     * @param AbstractFilter $filter
     * @param UiColumn|null $column
     * @return Filter|null
     */
    private function buildFieldByFilterComponent(AbstractFilter $filter, ?UiColumn $column): ?Field
    {
        return match (true) {
            $filter instanceof Search => $this->buildFieldBySearchFilter($filter),
            $filter instanceof Select => $this->buildFieldBySelectFilter($filter, $column),
            $filter instanceof Input => $this->buildFieldByInputFilter($filter, $column),
            $filter instanceof Range => $this->buildFieldByRangeFilter($filter, $column),
            $filter instanceof Date => $this->buildFieldByDateFilter($filter, $column),
            default => null,
        };
    }

    private function buildFieldBySearchFilter(Search $filter): Field
    {
        return new Field(
            $filter->getName(),
            'Search by any field',
            null,
            new Filter('fulltext'),
        );
    }

    private function buildFieldBySelectFilter(Select $filter, ?UiColumn $column): Field
    {
        return new Field(
            $filter->getName(),
            (string)$filter->getData('config/label'),
            $column ? $this->buildColumnByUiColumn($column) : null,
            new Filter('select', $this->getFilterOptions($filter, $column)),
        );
    }

    private function buildFieldByDateFilter(Date $filter, ?UiColumn $column): Field
    {
        return new Field(
            $filter->getName(),
            (string)$filter->getData('config/label'),
            $column ? $this->buildColumnByUiColumn($column) : null,
            new Filter('date'),
        );
    }

    private function buildFieldByRangeFilter(Range $filter, ?UiColumn $column): Field
    {
        return new Field(
            $filter->getName(),
            (string)$filter->getData('config/label'),
            $column ? $this->buildColumnByUiColumn($column) : null,
            new Filter('range'),
        );
    }

    private function buildFieldByInputFilter(Input $filter, ?UiColumn $column): Field
    {
        return new Field(
            $filter->getName(),
            (string)$filter->getData('config/label'),
            $column ? $this->buildColumnByUiColumn($column) : null,
            new Filter('input'),
        );
    }

    private function buildColumnByUiColumn(UiColumn $column): Column
    {
        $sortableConfig = $column->getData('config/sortable');
        $isSortable = !($sortableConfig !== null) || (bool)$sortableConfig;

        $visibleConfig = $column->getData('config/visible');
        $isVisible = !($visibleConfig !== null) || (bool)$visibleConfig;

        return new Column(
            $isSortable,
            $isVisible
        );
    }

    /**
     * Get filter options.
     *
     * Uses column options as fallback.
     *
     * @param AbstractFilter $filter
     * @param UiColumn|null $column
     * @return array
     */
    private function getFilterOptions(AbstractFilter $filter, ?UiColumn $column): array
    {
        $filterConfiguration = $filter->getConfiguration();
        $options = $filterConfiguration['options'] ?? null;

        if (!is_array($options) && $column) {
            $columnConfig = $column->getConfiguration();
            $options = $columnConfig['options'] ?? null;
        }

        $options = is_array($options) ? $options : [];
        $maxOptionsSize = $this->advancedConfig->getMaxOptionsSize();

        if ($maxOptionsSize > 0 && count($options) > $maxOptionsSize) {
            $options = array_slice($options, 0, $maxOptionsSize);
        }

        return array_filter(array_map(
            function ($option): ?array {
                if (!is_array($option)) {
                    return null;
                }

                return [
                    'label' => $option['label'] ?? null,
                    'value' => $option['value'] ?? null,
                ];
            },
            $options
        ));
    }

    /**
     * Call prepare method in the component UI
     *
     * @param UiComponentInterface $component
     * @return void
     */
    private function prepareComponent(UiComponentInterface $component)
    {
        foreach ($component->getChildComponents() as $child) {
            $this->prepareComponent($child);
        }
        $component->prepare();
    }
}
