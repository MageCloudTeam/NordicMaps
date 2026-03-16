<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\Customer\Controller\Address\File;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\Api\CustomAttributesDataInterface;
use Magento\Customer\Api\AddressMetadataInterface;
use Magento\Customer\Model\FileUploader;
use Magento\Customer\Model\FileUploaderFactory;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Exception\LocalizedException;
use Psr\Log\LoggerInterface;
use Magento\Customer\Model\FileProcessorFactory;

/**
 * Class for upload files for customer custom address attributes
 */
class Upload extends Action implements HttpPostActionInterface
{
    /**
     * @var FileUploaderFactory
     */
    private $fileUploaderFactory;

    /**
     * @var AddressMetadataInterface
     */
    private $addressMetadataService;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var FileProcessorFactory
     */
    private $fileProcessorFactory;

    /**
     * @param Context $context
     * @param FileUploaderFactory $fileUploaderFactory
     * @param AddressMetadataInterface $addressMetadataService
     * @param LoggerInterface $logger
     * @param FileProcessorFactory $fileProcessorFactory
     */
    public function __construct(
        Context $context,
        FileUploaderFactory $fileUploaderFactory,
        AddressMetadataInterface $addressMetadataService,
        LoggerInterface $logger,
        FileProcessorFactory $fileProcessorFactory
    ) {
        $this->fileUploaderFactory = $fileUploaderFactory;
        $this->addressMetadataService = $addressMetadataService;
        $this->logger = $logger;
        $this->fileProcessorFactory = $fileProcessorFactory;
        parent::__construct($context);
    }

    /**
     * @inheritDoc
     */
    public function execute()
    {
        try {
            $requestedFiles = $this->validateAndFilterUploadedFiles($this->getRequest()->getFiles('custom_attributes'));
            if (empty($requestedFiles)) {
                $result = $this->processError(__('No files for upload.'));
            } else {
                $attributeCode = key($requestedFiles);
                $attributeMetadata = $this->addressMetadataService->getAttributeMetadata($attributeCode);

                /** @var FileUploader $fileUploader */
                $fileUploader = $this->fileUploaderFactory->create([
                    'attributeMetadata' => $attributeMetadata,
                    'entityTypeCode' => AddressMetadataInterface::ENTITY_TYPE_ADDRESS,
                    'scope' => CustomAttributesDataInterface::CUSTOM_ATTRIBUTES,
                ]);

                $errors = $fileUploader->validate();
                if (true !== $errors) {
                    $errorMessage = implode('</br>', $errors);
                    $result = $this->processError(($errorMessage));
                } else {
                    $result = $fileUploader->upload();
                    $this->moveTmpFileToSuitableFolder($result);
                }
            }
        } catch (LocalizedException $e) {
            $result = $this->processError($e->getMessage(), $e->getCode());
        } catch (\Exception $e) {
            $this->logger->critical($e);
            $result = $this->processError($e->getMessage(), $e->getCode());
        }

        /** @var \Magento\Framework\Controller\Result\Json $resultJson */
        $resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);
        $resultJson->setData($result);
        return $resultJson;
    }
    /**
     * Validate and filter uploaded files to prevent security vulnerabilities
     *
     * Implements protection against CVE-2025-54236 (SessionReaper) by validating:
     * - File names (reject session files anywhere in name/path)
     * - Dangerous extensions (reject .php, .phtml, .php[3-8], .phar, .phpt anywhere in filename)
     * - Valid extensions (only allow files ending with .jpg, .jpeg, .png, .gif)
     * - MIME types (verify actual file content matches allowed image types)
     *
     * @param array|null $requestedFiles
     * @return array
     */
    private function validateAndFilterUploadedFiles(?array $requestedFiles): array
    {
        if ($requestedFiles === null || empty($requestedFiles)) {
            return [];
        }

        $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];
        $allowedMimeTypes = ['image/jpeg', 'image/png', 'image/gif'];

        foreach ($requestedFiles as $key => $fileInfo) {
                $fileName = $fileInfo['name'] ?? '';
                $fullPath = $fileInfo['full_path'] ?? $fileName;
                $tmpName = $fileInfo['tmp_name'] ?? '';
    
                // Check for session files anywhere in name or path
                $isPotentialSessionFile = strpos($fileName, 'sess_') !== false || strpos($fullPath, 'sess_') !== false;
    
                // Check for dangerous extensions anywhere in the filename (not just at the end)
                $dangerousExtensions = ['php', 'phtml', 'php3', 'php4', 'php5', 'php7', 'php8', 'phar', 'phpt', 'phps'];
                $hasDangerousExtension = false;
                foreach ($dangerousExtensions as $dangerousExt) {
                        if (preg_match('/\.' . preg_quote($dangerousExt, '/') . '($|\.)/i', $fullPath)) {
                                $hasDangerousExtension = true;
                                break;
                }
            }

            // Check that file ends with allowed extension
            $hasValidExtension = preg_match('/\.(' . implode('|', $allowedExtensions) . ')$/i', $fullPath);

            // Verify MIME type matches actual file content
            $mimeType = $tmpName && file_exists($tmpName) ? mime_content_type($tmpName) : '';
            $hasValidMimeType = in_array($mimeType, $allowedMimeTypes, true);

            if ($isPotentialSessionFile || $hasDangerousExtension || !$hasValidExtension || !$hasValidMimeType) {
                        unset($requestedFiles[$key]);
            }
        }

        return $requestedFiles;
    }

    /**
     * Move file from temporary folder to the 'customer_address' media folder
     *
     * @param array $fileInfo
     * @throws LocalizedException
     */
    private function moveTmpFileToSuitableFolder(&$fileInfo)
    {
        $fileName = $fileInfo['file'];
        $fileProcessor = $this->fileProcessorFactory
            ->create(['entityTypeCode' => AddressMetadataInterface::ENTITY_TYPE_ADDRESS]);

        $newFilePath = $fileProcessor->moveTemporaryFile($fileName);
        $fileInfo['file'] = $newFilePath;
        $fileInfo['url'] = $fileProcessor->getViewUrl(
            $newFilePath,
            'file'
        );
    }

    /**
     * Prepare result array for errors
     *
     * @param string $message
     * @param int $code
     * @return array
     */
    private function processError($message, $code = 0)
    {
        $result = [
            'error' => $message,
            'errorcode' => $code,
        ];

        return $result;
    }
}
