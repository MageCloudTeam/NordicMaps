<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */
declare(strict_types=1);

namespace Klarna\Kp\Model\QuoteProvider;

use Magento\Framework\App\RequestInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Quote\Api\Data\CartInterface;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\OrderRepositoryInterface;

/**
 * @internal
 */
class PersistenceAwareQuoteProvider implements QuoteProviderInterface
{
    /**
     * @var RequestInterface
     */
    private $request;
    /**
     * @var OrderRepositoryInterface
     */
    private $orderRepository;
    /**
     * @var CartRepositoryInterface
     */
    private $quoteRepository;

    /**
     * @param RequestInterface         $request
     * @param OrderRepositoryInterface $orderRepository
     * @param CartRepositoryInterface  $quoteRepository
     * @codeCoverageIgnore
     */
    public function __construct(
        RequestInterface         $request,
        OrderRepositoryInterface $orderRepository,
        CartRepositoryInterface  $quoteRepository
    ) {
        $this->request         = $request;
        $this->orderRepository = $orderRepository;
        $this->quoteRepository = $quoteRepository;
    }

    /**
     * @inheritDoc
     */
    public function getQuote(): ?CartInterface
    {
        try {
            return $this->getQuoteFromRepository();
        } catch (NoSuchEntityException $e) {
            return null;
        }
    }

    /**
     * Fetches the Quote from the Repository
     *
     * @throws NoSuchEntityException
     */
    private function getQuoteFromRepository(): ?CartInterface
    {
        $order = $this->getOrder();
        if (!$order instanceof OrderInterface) {
            return null;
        }

        return $this->quoteRepository->get($order->getQuoteId());
    }

    /**
     * Fetches the Order from the orderRepository using the order_id taken from the request
     *
     * @throws NoSuchEntityException
     */
    private function getOrder(): ?OrderInterface
    {
        $id = $this->request->getParam('order_id');
        if (!$id) {
            return null;
        }

        return $this->orderRepository->get($id);
    }
}
