<?php
/**
 * Copyright © tons of limes <https://github.com/tons-of-limes>. All rights reserved.
 */
declare(strict_types=1);

namespace TonsOfLimes\AdminGridAI\Model;

use JsonException;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\NoSuchEntityException;
use OpenAI\Responses\Chat\CreateResponse;
use TonsOfLimes\AdminGridAI\Model\LLM\GatewayClientFactory;
use TonsOfLimes\AdminGridAI\Model\Prompt\GetPrompt;
use TonsOfLimes\AdminGridAI\Model\Ui\GetListingComponentStructure;
use TonsOfLimes\AdminGridAI\Model\Ui\RenderListingStructure;

/**
 * Service to build listing state (filters, sorting, columns) by user query.
 */
class BuildListingStateByQuery
{
    private const PROMPT_CODE = 'listing_state_query';

    public function __construct(
        private readonly GatewayClientFactory $gatewayClientFactory,
        private readonly GetListingComponentStructure $getListingComponentStructure,
        private readonly GetPrompt $getPrompt,
        private readonly RenderListingStructure $renderListingStructure,
    ) {}

    /**
     * @param string $componentName
     * @param string $query
     * @return array|null
     * @throws LLM\Exception\GatewayConfigException
     * @throws InputException
     * @throws NoSuchEntityException
     */
    public function execute(string $componentName, string $query): ?array
    {
        $structure = $this->getListingComponentStructure->execute($componentName);
        $structureRendered = $this->renderListingStructure->execute($structure);

        $systemPrompt = $this->getPrompt->execute(
            self::PROMPT_CODE,
            [
                'listing_structure' => $structureRendered,
            ]
        );

        return $this->call($query, $systemPrompt);
    }

    /**
     * @throws LLM\Exception\GatewayConfigException
     */
    private function call(string $query, string $systemPrompt): ?array
    {
        $client = $this->gatewayClientFactory->create();

        $response = $client->chat()->create([
            'model' => 'gpt-5.1',
            'messages' => [
                [
                    'role' => 'system',
                    'content' => $systemPrompt,
                ],
                [
                    'role' => 'user',
                    'content' => $query
                ]
            ],
            'response_format' => [
                'type' => 'json_schema',
                'json_schema' => $this->getResponseSchema(),
            ],
        ]);

        return $this->parseResponse($response);
    }

    /**
     * Parse response in JSON format.
     *
     * @param CreateResponse $response
     * @return array|null
     */
    private function parseResponse(CreateResponse $response): ?array
    {
        $choice = $response->choices[0] ?? null;
        if (!$choice) {
            return null;
        }

        $content = $choice->message->content;

        try {
            return json_decode($content, true, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException) {
            return null;
        }
    }

    /**
     * Get response schema.
     *
     * @return array
     */
    private function getResponseSchema(): array
    {
        return [
            'name' => 'root',
            'strict' => true,
            'schema' => [
                'type' => 'object',
                'properties' => [
                    'sorting' => [
                        'type' => ['object', 'null'],
                        'properties' => [
                            'field' => [
                                'type' => 'string',
                            ],
                            'direction' => [
                                'type' => 'string',
                                'enum' => [
                                    'asc',
                                    'desc',
                                ]
                            ],
                        ],
                        'required' => ['field', 'direction'],
                        'additionalProperties' => false,
                    ],
                    'columns' => [
                        'type' => ['array', 'null'],
                        'items' => [
                            'type' => 'string',
                        ]
                    ],
                    'filters' => [
                        'type' => 'array',
                        'items' => [
                            'type' => 'object',
                            'properties' => [
                                'code' => [
                                    'type' => 'string',
                                ],
                                'value' => [
                                    'anyOf' => [
                                        [
                                            'type' => 'string',
                                        ],
                                        [
                                            'type' => 'array',
                                            'items' => [
                                                'type' => 'string',
                                            ],
                                        ],
                                        [
                                            'type' => 'object',
                                            'properties' => [
                                                'from' => [
                                                    'type' => 'string'
                                                ],
                                                'to' => [
                                                    'type' => 'string'
                                                ],
                                            ],
                                            'required' => ['from', 'to'],
                                            'additionalProperties' => false,
                                        ]
                                    ]
                                ]
                            ],
                            'required' => ['code', 'value'],
                            'additionalProperties' => false,
                        ]
                    ]
                ],
                'additionalProperties' => false,
                'required' => ['filters', 'columns', 'sorting'],
            ],
        ];
    }
}
