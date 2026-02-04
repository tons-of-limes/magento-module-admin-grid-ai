<?php
/**
 * Copyright © tons of limes <https://github.com/tons-of-limes>. All rights reserved.
 */
declare(strict_types=1);

namespace TonsOfLimes\AdminGridAI\Model\Ui;

use TonsOfLimes\AdminGridAI\Model\Ui\Data\Field;
use TonsOfLimes\AdminGridAI\Model\Ui\Data\Filter;
use TonsOfLimes\AdminGridAI\Model\Ui\Data\ListingStructure;

/**
 * Service to render listing structure definition as string.
 */
class RenderListingStructure
{
    /**
     * Render listing structure definition.
     */
    public function execute(ListingStructure $structure): string
    {
        $fieldsData = $this->convertFieldsToPlainArray($structure->getFields());
        $fieldsAsString = $this->exportArrayAsPSV($fieldsData);
        $multiSelectAsString = $this->renderBool($structure->isMultiSelectEnabled());

        return <<<PROMPT
Listing name: {$structure->getName()}
Multiselect enabled: {$multiSelectAsString}
Fields config (as PSV)
{$fieldsAsString}
PROMPT;
    }

    private function exportArrayAsPSV(array $rows): string
    {
        $rowsStr = array_map(
            function (array $row): string {
                return implode(' | ', $row);
            },
            $rows
        );

        return implode("\n", $rowsStr);
    }

    /**
     * Convert fields to plain array for rendering.
     *
     * @param Field[] $fields
     * @return string[][]
     */
    private function convertFieldsToPlainArray(array $fields): array
    {
        $data = [
            [
                'name',
                'label',
                'column_exists',
                'column_sortable',
                'column_visible',
                'filter_exists',
                'filter_type',
                'filter_options'
            ],
        ];

        foreach ($fields as $field) {
            $data[] = [
                $field->getName(),
                $field->getLabel(),
                $this->renderBool((bool) $field->getColumn()),
                $this->renderBool((bool) $field->getColumn()?->isSortable()),
                $this->renderBool((bool) $field->getColumn()?->isVisible()),
                $this->renderBool((bool) $field->getFilter()),
                $field->getFilter()?->getType(),
                $field->getFilter() ? $this->renderFilterOptions($field->getFilter()) : '',
            ];
        }

        return $data;
    }

    /**
     * Render boolean to "yes"/"no" string.
     */
    private function renderBool(bool $value): string
    {
        return $value ? 'yes' : 'no';
    }

    /**
     * Render filter options as comma separated string.
     *
     * @param Filter $filter
     * @return string
     */
    private function renderFilterOptions(Filter $filter): string
    {
        $optionsParts = array_map(
            function (array $option): string {
                $value = $option['value'] ?? null;
                $renderedValues = is_string($value) ? $value : '';

                return sprintf('label: %s, value: "%s"', $option['label'] ?? '', $renderedValues);
            },
            $filter->getOptions()
        );

        return implode(', ', $optionsParts);
    }
}
