<?php
/**
 * BSS Commerce Co.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://bsscommerce.com/Bss-Commerce-License.txt
 *
 * @category  BSS
 * @package   Bss_FastOrder
 * @author    Extension Team
 * @copyright Copyright (c) 2017-2018 BSS Commerce Co. ( http://bsscommerce.com )
 * @license   http://bsscommerce.com/Bss-Commerce-License.txt
 */
namespace Bss\FastOrder\Controller\Index;

use Magento\Framework\Controller\ResultFactory;
use Psr\Log\LoggerInterface;

/**
 * Class Csv
 * @package Bss\FastOrder\Controller\Index
 */
class Csv extends \Magento\Framework\App\Action\Action
{
    /**
     * @var \Bss\FastOrder\Model\Search\Save
     */
    protected $saveModel;

    /**
     * @var \Magento\Framework\App\CacheInterface
     */
    protected $cache;

    /**
     * @var \Magento\Framework\Pricing\Helper\Data
     */
    protected $pricingHelper;

    /**
     * @var \Magento\MediaStorage\Model\File\UploaderFactory
     */
    protected $fileUploaderFactory;

    /**
     * @var \Bss\FastOrder\Helper\Data
     */
    protected $helperBss;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * Csv constructor.
     *
     * @param \Magento\Framework\App\Action\Context            $context
     * @param \Magento\Framework\App\CacheInterface            $cache
     * @param \Magento\Framework\Pricing\Helper\Data           $pricingHelper
     * @param \Magento\MediaStorage\Model\File\UploaderFactory $fileUploaderFactory
     * @param \Bss\FastOrder\Helper\Data                       $helperBss
     * @param \Bss\FastOrder\Model\Search\Save                 $saveModel
     * @param LoggerInterface $logger
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\App\CacheInterface $cache,
        \Magento\Framework\Pricing\Helper\Data $pricingHelper,
        \Magento\MediaStorage\Model\File\UploaderFactory $fileUploaderFactory,
        \Bss\FastOrder\Helper\Data $helperBss,
        \Bss\FastOrder\Model\Search\Save $saveModel,
        LoggerInterface $logger
    ) {
        parent::__construct($context);
        $this->pricingHelper = $pricingHelper;
        $this->cache = $cache;
        $this->saveModel = $saveModel;
        $this->fileUploaderFactory = $fileUploaderFactory;
        $this->helperBss = $helperBss;
        $this->logger = $logger;
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $result = [];
        try {
            // csv function support only simple product not custom option
            $uploader = $this->fileUploaderFactory->create(['fileId' => 'file']);
            $file = $uploader->validateFile();
            if ($this->checkError($file)) {
                return;
            }
            $readCsv = trim(file_get_contents($file['tmp_name']));
            $csvLines = explode("\n", $readCsv);
            $delimiter = $this->_getDelimiter($csvLines[0]);
            $csvFirstLine = explode($delimiter, $csvLines[0]);
            if ($csvFirstLine[0] != 'sku' && $csvFirstLine[1] != 'qty') {
                $this->messageManager->addErrorMessage(
                    __('The file\'s format is not correct. Please download sample csv file and try again.')
                );
                return;
            }
            array_shift($csvLines);
            // foreach row file csv
            $res = $this->getResponseCsv($csvLines);
            $skuNotSp = $res[0];
            $skuNotExist = $res[1];
            $result = $res[2];

            // mess error sku products not support
            if ($skuNotSp) {
                $verbs = 'is';
                $skuNotSp = rtrim($skuNotSp, '&nbsp;');
                $skuNotSp = rtrim($skuNotSp, ',');
                if (count(explode(',', $skuNotSp)) > 1) {
                    $verbs = 'are';
                }
                $this->messageManager->addErrorMessage(
                    __(
                        'CSV import is only available for simple product(s) without custom option(s). %1 %2 not supported.',
                        $skuNotSp,
                        $verbs
                    )
                );
            }
            // mess error sku products not exist
            if ($skuNotExist) {
                $skuNotExist = rtrim($skuNotExist, '&nbsp;');
                $skuNotExist = rtrim($skuNotExist, ',');
                $this->messageManager->addErrorMessage(__('%1 do not match or do not exist on the site.', $skuNotExist));
            }
            $this->messageManager->addSuccessMessage(__('Import Complete.'));
            if (count($result) == 0) {
                $this->messageManager->addErrorMessage(__('No Item Imported.'));
            }
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage(__('Something went wrong while reading file.'));
            $this->logger->critical($e);
        }

        $resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);
        $resultJson->setData($result);

        return $resultJson;
    }

    /**
     * @param  $csvFirstLine
     * @return mixed|string
     */
    protected function _getDelimiter($csvFirstLine)
    {
        $delimiter = ',';
        $delimiters = [',','\t',';','|',':'];
        foreach ($delimiters as $value) {
            if (strpos($csvFirstLine, $value) !== false) {
                $delimiter = $value;
                break;
            }
        }
        return $delimiter;
    }

    /**
     * @param null $csvLines
     * @return array
     */
    protected function getResponseCsv($csvLines = null)
    {
        $skuNotSp = '';
        $skuNotExist = '';
        $delimiter = $this->_getDelimiter($csvLines[0]);
        $result = [];
        $i = 0;
        foreach ($csvLines as $csvLine) {
            $arrLine = explode($delimiter, $csvLine);
            $sku = $arrLine[0];
            $qty = $arrLine[1];

            if (!$sku) {
                continue;
            }
            if (empty($qty)) {
                $qty = 1;
            }
            $productData = $this->saveModel->getProductBySku($sku, true);

            if (empty($productData)) {
                $skuNotExist .= $sku . ',&nbsp;';
                continue;
            } else {
                array_push($result, $productData);
                $result[$i][1] = $qty;
                $i++;
            }
        }
        $res = [$skuNotSp, $skuNotExist, $result];
        return $res;
    }

    /**
     * @param  null $file
     * @return bool
     */
    protected function checkError($file = null)
    {
        if (!is_array($file) || empty($file)) {
            $this->messageManager->addErrorMessage(__('We can\'t import item to your table right now.'));
            return true;
        }

        if ($file['error'] > 0) {
            $this->messageManager->addErrorMessage(__('We can\'t import item to your table right now.'));
            return true;
        }
        if (pathinfo($file['name'], PATHINFO_EXTENSION) != 'csv') {
            $this->messageManager->addErrorMessage(
                __('The file\'s format is not correct. Please download sample csv file and try again.')
            );
            return true;
        }
        return false;
    }
}
