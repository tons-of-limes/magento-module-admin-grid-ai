<?php
/**
 * Copyright © tons of limes <https://github.com/tons-of-limes>. All rights reserved.
 */
declare(strict_types=1);

namespace TonsOfLimes\AdminGridAI\Controller\Adminhtml\Grid;

use TonsOfLimes\AdminGridAI\Config\GeneralConfig;
use TonsOfLimes\AdminGridAI\Model\BuildListingStateByQuery;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\ResultInterface;

/**
 * Build listing state by query.
 */
class BuildStateByQuery extends Action implements HttpPostActionInterface
{
    /**
     * @inheritdoc
     */
    const ADMIN_RESOURCE = 'TonsOfLimes_AdminGridAI::grid';

    public function __construct(
        Context $context,
        private readonly GeneralConfig $generalConfig,
        private readonly BuildListingStateByQuery $buildListingStateByQuery,
    ) {
        parent::__construct($context);
    }

    /**
     * @inheritdoc
     */
    public function execute(): ResultInterface
    {
        /** @var Json $jsonResult */
        $result = $this->resultFactory->create(ResultFactory::TYPE_JSON);

        if (!$this->generalConfig->isEnabled()) {
            $result->setData([
                'success' => false,
                'error' => [
                    'message' => __(
                        'The extension is disabled.'
                        . ' Go to [Stores > Configuration > tons of limes 🍋‍🟩 > Admin Grid AI > General]'
                        . ' to enable it.'
                    ),
                ]
            ]);

            return $result;
        }

        $componentName = $this->getRequest()->getParam('namespace');
        $query = $this->getRequest()->getParam('query');

        try {
            $state = $this->buildListingStateByQuery->execute($componentName, $query);
        } catch (\Exception $e) {
            $result->setData([
                'success' => false,
                'error' => [
                    'message' => $e->getMessage(),
                ]
            ]);

            return $result;
        }

        $result->setData([
            'success' => true,
            'data' => $state,
        ]);

        return $result;
    }
}
