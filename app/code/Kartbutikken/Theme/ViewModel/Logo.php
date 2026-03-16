<?php
/**
 * Copyright (c) 2019. Volodymyr Hryvinskyi.  All rights reserved.
 * @author: <mailto:volodymyr@hryvinskyi.com>
 * @github: <https://github.com/hryvinskyi>
 */

declare(strict_types=1);

namespace Kartbutikken\Theme\ViewModel;

use Kartbutikken\Theme\Model\Config\Design\Header;
use Magento\Config\Model\Config\Backend\Image\Logo as ImageLogo;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\MediaStorage\Helper\File\Storage\Database;

/**
 * Class Logo
 */
class Logo implements ArgumentInterface
{
    /**
     * @var string
     */
    private $logoSrc;

    /**
     * @var \Magento\Framework\Filesystem\Directory\Read
     */
    private $mediaDirectory;

    /**
     * @var Header
     */
    private $headerConfig;

    /**
     * Url Builder
     *
     * @var UrlInterface
     */
    private $urlBuilder;

    /**
     * @var Database
     */
    private $fileStorageHelper;

    /**
     * Filesystem instance
     *
     * @var Filesystem
     */
    private $filesystem;

    /**
     * Logo constructor.
     *
     * @param Header $headerConfig
     * @param UrlInterface $urlBuilder
     * @param Database $fileStorageHelper
     * @param Filesystem $filesystem
     */
    public function __construct(
        Header $headerConfig,
        UrlInterface $urlBuilder,
        Database $fileStorageHelper,
        Filesystem $filesystem
    ) {
        $this->headerConfig = $headerConfig;
        $this->urlBuilder = $urlBuilder;
        $this->fileStorageHelper = $fileStorageHelper;
        $this->filesystem = $filesystem;
    }

    /**
     * Return header config
     *
     * @return Header
     */
    public function getHeaderConfig(): Header
    {
        return $this->headerConfig;
    }

    /**
     * Get logo image URL
     *
     * @return string
     * @throws \Magento\Framework\Exception\ValidatorException
     */
    public function getLogoSrc()
    {
        if ($this->logoSrc === null) {
            $folderName = ImageLogo::UPLOAD_DIR;
            $storeLogoPath = $this->getHeaderConfig()->getLogoSrc();
            $path = $folderName . '/' . $storeLogoPath;
            $logoUrl = $this->urlBuilder->getBaseUrl(['_type' => UrlInterface::URL_TYPE_MEDIA]) . $path;

            if ($storeLogoPath !== null && $this->isFileExist($path)) {
                $this->logoSrc = $logoUrl;
            }
        }

        return $this->logoSrc;
    }

    /**
     * If DB file storage is on - find there, otherwise - just file_exists
     *
     * @param string $filename relative path
     *
     * @return bool
     * @throws \Magento\Framework\Exception\ValidatorException
     */
    public function isFileExist(string $filename)
    {

        if ($this->fileStorageHelper->checkDbUsage() && !$this->getMediaDirectory()->isFile($filename)) {
            $this->fileStorageHelper->saveFileToFilesystem($filename);
        }

        return $this->getMediaDirectory()->isFile($filename);
    }

    /**
     * Get media directory
     *
     * @return \Magento\Framework\Filesystem\Directory\Read
     */
    public function getMediaDirectory()
    {
        if (!$this->mediaDirectory) {
            $this->mediaDirectory = $this->filesystem->getDirectoryRead(DirectoryList::MEDIA);
        }

        return $this->mediaDirectory;
    }
}