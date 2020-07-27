<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\MediaGalleryUi\Model\UpdateAsset;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Filesystem;
use Magento\MediaGalleryMetadataApi\Model\AddMetadataComposite;
use Magento\MediaGalleryMetadataApi\Api\Data\MetadataInterface;
use Psr\Log\LoggerInterface;

class SaveMetadataToFile
{
    /**
     * @var Filesystem
     */
    private $filesystem;

    /**
     * @var AddMetadataComposite
     */
    private $addMetadata;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @param Filesystem $filesystem
     * @param AddMetadataComposite $addMetadata
     * @param LoggerInterface $logger
     */
    public function __construct(
        Filesystem $filesystem,
        AddMetadataComposite $addMetadata,
        LoggerInterface $logger
    ) {
        $this->filesystem = $filesystem;
        $this->addMetadata = $addMetadata;
        $this->logger = $logger;
    }

    /**
     * Save updated metadata
     *
     * @param string $path
     * @param MetadataInterface $data
     */
    public function execute(string $path, MetadataInterface $data): void
    {
        $absolutePath = $this->filesystem->getDirectoryRead(DirectoryList::MEDIA)->getAbsolutePath($path);

        try {
            $this->addMetadata->execute($absolutePath, $data);
        } catch (LocalizedException $e) {
            $this->logger->critical($e);
        }
    }
}
