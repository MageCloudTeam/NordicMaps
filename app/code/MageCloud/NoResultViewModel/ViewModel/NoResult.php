<?php
namespace MageCloud\NoResultViewModel\ViewModel;

use Magento\Cms\Api\BlockRepositoryInterface;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Store\Model\StoreManagerInterface;

class NoResult implements ArgumentInterface
{
    /**
     * Store manager
     *
     * @var StoreManagerInterface
     */
    protected $_storeManager;

    /**
     *
     * @var BlockRepositoryInterface
     */
    protected $_blockRepository;

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Cms\Api\BlockRepositoryInterface $blockRepository
    ) {
        $this->_storeManager = $storeManager;
        $this->_blockRepository = $blockRepository;

    }
    public function noResult(): string
    {
        $no = $this->_blockRepository->getById('22');
        $en = $this->_blockRepository->getById('23');
        $de = $this->_blockRepository->getById('24');

        $storeCode = $this->_storeManager->getStore()->getId();
        if ($storeCode === '1') {
            return $no->getContent();
        }
        if ($storeCode === '2') {
            return $en->getContent();
        }
        if ($storeCode === '3') {
            return $de->getContent();
        }
        return $no->getContent();
    }
}
