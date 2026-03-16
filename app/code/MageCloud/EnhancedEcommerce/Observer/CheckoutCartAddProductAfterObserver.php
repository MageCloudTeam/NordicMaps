<?php
/**
 * @author andy
 * @email andyworkbase@gmail.com
 * @team MageCloud
 * @package MageCloud_EnhancedEcommerce
 */
namespace MageCloud\EnhancedEcommerce\Observer;

use MageCloud\EnhancedEcommerce\Model\EventManager;
use MageCloud\EnhancedEcommerce\Model\EventManagerFactory;
use Magento\Catalog\Model\Product;
use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer;
use Magento\Framework\Exception\NoSuchEntityException;
use MageCloud\EnhancedEcommerce\Model\EventResolver\AddToCart;
use Magento\Quote\Model\Quote\Item;

/**
 * Class CheckoutCartAddProductAfterObserver
 * @package MageCloud\EnhancedEcommerce\Observer
 */
class CheckoutCartAddProductAfterObserver implements ObserverInterface
{
    /**
     * @var EventManagerFactory
     */
    private $eventManagerFactory;

    /**
     * @var CheckoutSession
     */
    private $checkoutSession;

    /**
     * @param EventManagerFactory $eventManagerFactory
     * @param CheckoutSession $checkoutSession
     */
    public function __construct(
        EventManagerFactory $eventManagerFactory,
        CheckoutSession $checkoutSession
    ) {
        $this->eventManagerFactory = $eventManagerFactory;
        $this->checkoutSession = $checkoutSession;
    }

    /**
     * @param Observer $observer
     * @return void
     * @throws NoSuchEntityException
     */
    public function execute(Observer $observer): void
    {
        /** @var Item $quioteItem */
        $quoteItem = $observer->getEvent()->getQuoteItem();
        /** @var Product $product */
        $product = $observer->getEvent()->getProduct();

        /** @var EventManager $eventManager */
        $eventManager = $this->eventManagerFactory->create(
            [
                'eventArguments' => [
                    'event_type' => AddToCart::EVENT_TYPE,
                    'quote_item' => $quoteItem,
                    'product' => $product
                ]
            ]
        );
        $this->checkoutSession->setAddToCartDataLayerEventData(null);
        $this->checkoutSession->setAddToCartDataLayerEventData(
            $eventManager->setEvenstInitializedCount()
                ->initEvent()
        );
    }
}
