<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\AdobeStockImageAdminUi\Controller\Adminhtml\Preview;

use Magento\AdobeStockAssetApi\Api\GetAssetByIdInterface;
use Magento\Backend\App\Action;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Exception\LocalizedException;
use Psr\Log\LoggerInterface;

/**
 * Retrieve image path by identifier
 */
class GetPath extends Action
{
    private const HTTP_OK = 200;
    private const HTTP_BAD_REQUEST = 400;
    private const HTTP_INTERNAL_ERROR = 500;

    /**
     * @see _isAllowed()
     */
    public const ADMIN_RESOURCE = 'Magento_AdobeStockImageAdminUi::save_preview_images';

    /**
     * @var GetAssetByIdInterface
     */
    private $getAssetById;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @param Action\Context $context
     * @param LoggerInterface $logger
     * @param GetAssetByIdInterface $getAssetById
     */
    public function __construct(
        Action\Context $context,
        LoggerInterface $logger,
        GetAssetByIdInterface $getAssetById
    ) {
        parent::__construct($context);

        $this->getAssetById = $getAssetById;
        $this->logger = $logger;
    }

    /**
     * @inheritdoc
     */
    public function execute()
    {
        try {
            $params = $this->getRequest()->getParams();

            $document = $this->getAssetById->execute((int) $params['media_id']);

            $pathAttribute = $document->getCustomAttribute('path');
            $pathValue = $pathAttribute->getValue();

            $responseCode = self::HTTP_OK;
            $responseContent = [
                'success' => true,
                'result' => $pathValue ?? null,
            ];
        } catch (LocalizedException $exception) {
            $responseCode = self::HTTP_BAD_REQUEST;
            $responseContent = [
                'success' => false,
                'message' => $exception->getMessage(),
            ];
        } catch (\Exception $exception) {
            $responseCode = self::HTTP_INTERNAL_ERROR;
            $this->logger->critical($exception);
            $responseContent = [
                'success' => false,
                'message' => __('An error occurred on attempt to retirieve image path.'),
            ];
        }

        /** @var Json $resultJson */
        $resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);
        $resultJson->setHttpResponseCode($responseCode);
        $resultJson->setData($responseContent);

        return $resultJson;
    }
}
