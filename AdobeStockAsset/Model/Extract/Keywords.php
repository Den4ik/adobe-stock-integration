<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\AdobeStockAsset\Model\Extract;

use Magento\AdobeMediaGalleryApi\Api\Data\KeywordInterface;
use Magento\Framework\Api\Search\DocumentInterface;
use Magento\AdobeMediaGalleryApi\Api\Data\KeywordInterfaceFactory;

/**
 * Keywords extractor
 */
class Keywords
{
    private const DOCUMENT_FIELD_KEYWORDS = 'keywords';
    private const DOCUMENT_FIELD_KEYWORD_NAME = 'name';

    private const KEYWORD_FIELD_KEYWORD_NAME = 'keyword';

    /**
     * @var KeywordInterfaceFactory
     */
    private $keywordFactory;

    /**
     * @param KeywordInterfaceFactory $keywordFactory
     */
    public function __construct(
        KeywordInterfaceFactory $keywordFactory
    ) {
        $this->keywordFactory = $keywordFactory;
    }

    /**
     * Convert search document to the asset object
     *
     * @param DocumentInterface $document
     * @return KeywordInterface[]
     */
    public function convert(DocumentInterface $document): array
    {
        $attribute = $document->getCustomAttribute(self::DOCUMENT_FIELD_KEYWORDS);

        if (!$attribute || !is_array($attribute->getValue())) {
            return [];
        }

        $keywords = [];
        foreach ($attribute->getValue() as $keywordData) {
            $keywords[] = $this->keywordFactory->create(
                [
                    'data' => [
                        self::KEYWORD_FIELD_KEYWORD_NAME => $keywordData[self::DOCUMENT_FIELD_KEYWORD_NAME]

                    ]
                ]
            );
        }

        return $keywords;
    }
}
