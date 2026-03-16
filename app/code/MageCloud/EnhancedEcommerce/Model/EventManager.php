<?php
/**
 * @author andy
 * @email andyworkbase@gmail.com
 * @team MageCloud
 * @package MageCloud_EnhancedEcommerce
 */
namespace MageCloud\EnhancedEcommerce\Model;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Class DataLayerManager
 * @package MageCloud\EnhancedEcommerce\Model
 */
class EventManager
{
    /**
     * @var EventResolverProvider
     */
    private $eventResolverProvider;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var array
     */
    private $eventArguments = [];

    /**
     * @var bool
     */
    private $eventsInitializedCount = 0;

    /**
     * @param EventResolverProvider $eventResolverProvider
     * @param StoreManagerInterface $storeManager
     * @param array $eventArguments
     */
    public function __construct(
        EventResolverProvider $eventResolverProvider,
        StoreManagerInterface $storeManager,
        array $eventArguments = []
    ) {
        $this->eventResolverProvider = $eventResolverProvider;
        $this->storeManager = $storeManager;
        $this->eventArguments = $eventArguments;
    }

    /**
     * @return bool
     */
    public function getEvenstInitializedCount()
    {
        return $this->eventsInitializedCount;
    }

    /**
     * @param $state
     * @return $this
     */
    public function setEvenstInitializedCount($qty = 1)
    {
        $this->eventsInitializedCount += $qty;
        return $this;
    }

    /**
     * @return $this
     */
    public function resetEvenstInitializedCount()
    {
        $this->eventsInitializedCount = 0;
        return $this;
    }

    /**
     * @return string
     * @throws NoSuchEntityException
     */
    public function initEvent(): string
    {
        if ($this->eventsInitializedCount > 1) {
            // prevent execution multiple events at the same time which can cause an unexpected results
            return '';
        }
        if (!isset($this->eventArguments['store'])) {
            $this->eventArguments = array_merge(['store' => $this->storeManager->getStore()], $this->eventArguments);
        }
        $eventType = $this->eventArguments['event_type'] ?? null;
        /** @var EventResolverInterface $resolver */
        if ($resolver = $this->eventResolverProvider->getResolver($eventType)) {
            return $resolver->resolve($this->eventArguments);
        }
        return '';
    }
}