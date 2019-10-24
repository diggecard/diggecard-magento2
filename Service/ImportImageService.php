<?php
/**
 * @author Elogic Team
 * @copyright Copyright (c) 2019 Elogic (https://elogic.co)
 */

namespace Diggecard\Giftcard\Service;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem\Io\File;
use Magento\Framework\Exception\FileSystemException;
use Exception;

/**
 * Class ImportImageService
 *
 * @package Diggecard\Giftcard\Service
 */
class ImportImageService
{
    /**
     * Directory List
     *
     * @var DirectoryList
     */
    protected $directoryList;
    /**
     * File interface
     *
     * @var File
     */
    protected $file;

    /**
     * ImportImageService constructor
     *
     * @param DirectoryList $directoryList
     * @param File $file
     */
    public function __construct(
        DirectoryList $directoryList,
        File $file
    )
    {
        $this->directoryList = $directoryList;
        $this->file = $file;
    }

    /**
     * @param $product
     * @param $imageUrl
     * @param bool $visible
     * @param array $imageType
     * @return bool|string
     * @throws Exception
     */
    public function execute($product, $imageUrl, $visible = false, $imageType = [])
    {

        $importedImage = $this->importImage($imageUrl);
        $result = $importedImage['result'];
        $newFileName = $importedImage['newFileName'];

        if ($result) {
            /** @var ProductInterface $product */
            $product->addImageToMediaGallery($newFileName, $imageType, true, $visible);
        }
        return $result;
    }

    /**
     * @param $imageUrl
     * @return array
     * @throws FileSystemException
     * @throws Exception
     */
    public function importImage($imageUrl)
    {
        /** @var string $tmpDir */
        $tmpDir = $this->getMediaDirTmpDir();
        /** create folder if it is not exists */
        $this->file->checkAndCreateFolder($tmpDir);
        /** @var string $newFileName */
        $newFileName = $tmpDir . DIRECTORY_SEPARATOR . baseName($imageUrl);
        /** read file from URL and copy it to the new destination */
        $result = $this->file->read($imageUrl, $newFileName);

        return ['newFileName' => $newFileName, 'result' => $result];
    }

    /**
     * @return string
     * @throws FileSystemException
     */
    protected function getMediaDirTmpDir()
    {
        return $this->directoryList->getPath(DirectoryList::MEDIA) . DIRECTORY_SEPARATOR . 'tmp';
    }
}