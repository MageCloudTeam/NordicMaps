<?php
namespace MageCloud\OrderEmailCopy\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\ProductFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Sales\Api\Data\OrderItemInterface;
use MageCloud\OrderEmailCopy\Model\Config;
use Magento\Framework\Serialize\SerializerInterface;
use Magento\Sales\Model\OrderFactory;

class SalesOrderAfterSave implements ObserverInterface
{

    /**
     * @var Config
     */
    private $config;

    /**
     * @var SerializerInterface
     */
    private $serializer;

    public function __construct(
        Config $config,
        OrderFactory $orderModel,
        SerializerInterface $serializer
    ) {
        $this->config = $config;
        $this->orderModel = $orderModel;
        $this->serializer = $serializer;
    }

    /**
     * After save observer for order
     *
     * @param Observer $observer
     * @throws LocalizedException
     */
    public function execute(Observer $observer)
    {
        //$order = $observer->getEvent()->getOrder();

        if ($this->config->isEnabled() === false) {
            return;
        }

        $orderIds = $observer->getEvent()->getOrderIds();
        if(count($orderIds))
        {
            $order = $this->orderModel->create()->load($orderIds[0]);
        } else {
            return;
        }

        $brandCodes = '';
        /** @var OrderItemInterface $item */
        foreach ($order->getAllVisibleItems() as $key => $item) {
            $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
            $productResource = $objectManager->get('Magento\Catalog\Model\ResourceModel\Product');

            /** @var Product $product */
            $product = $item->getProduct();
            if (null !== $product->getCustomAttribute('map_series')) {
                $map_series =  $product->getCustomAttribute('map_series')->getValue();
                $brandCodes .= $map_series . ',';
            }
        }
        $processingRules = $this->config->getProcessingRules();
        $listEmailsToSendCopy = [];
        foreach ($this->serializer->unserialize($processingRules) as $key => $item) {
            if (str_contains($brandCodes, $item['brand_code'])) {
                $listEmailsToSendCopy[] = $item['brand_email'];
            }
        }

        $listEmailsToSendCopy = array_unique($listEmailsToSendCopy);

        if(!empty($listEmailsToSendCopy)) {
            foreach ($listEmailsToSendCopy as $emailAddress) {
                $order->setCustomerEmail($emailAddress);
                    try {
                        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
                        $objectManager->create('\Magento\Sales\Model\OrderNotifier')->notify($order);
                        //echo 'You sent the order email.';
                    } catch (\Magento\Framework\Exception\LocalizedException $e) {
                        echo($e->getMessage());
                    } catch (\Exception $e) {
                       // echo(__('We can\'t send the email order right now.'));
                        echo($e->getMessage());
                    }
            }
        }


        return $this;
    }
}
