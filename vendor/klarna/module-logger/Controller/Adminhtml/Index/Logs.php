<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */
declare(strict_types=1);

namespace Klarna\Logger\Controller\Adminhtml\Index;

use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\View\Result\Page;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\App\Action\HttpGetActionInterface;

/**
 * @SuppressWarnings(PHPMD.AllPurposeAction)
 * @api
 */
class Logs implements HttpGetActionInterface
{
    /**
     * @var PageFactory
     */
    private $pageFactory;

    /**
     * @param PageFactory $rawFactory
     * @codeCoverageIgnore
     */
    public function __construct(
        PageFactory $rawFactory
    ) {
        $this->pageFactory = $rawFactory;
    }

    /**
     * Klarna logs index page
     *
     * @return ResponseInterface|ResultInterface|Page
     */
    public function execute()
    {
        $resultPage = $this->pageFactory->create();
        $resultPage->setActiveMenu('Klarna_Logger::system_logging');
        $resultPage->getConfig()->getTitle()->prepend(__('Klarna Logs'));
        return $resultPage;
    }
}
