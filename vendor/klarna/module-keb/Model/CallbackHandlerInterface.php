<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */

namespace Klarna\Keb\Model;

use Klarna\Base\Exception;
use Klarna\Base\Model\Api\Exception as ApiException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Quote\Api\Data\CartInterface;

/**
 * @internal
 */
interface CallbackHandlerInterface
{
    /**
     * The address callback data will be used to have a quote fully prepared for the order place.
     *
     * @param CartInterface $quote
     * @param array $addressData
     * @return string
     * @throws ApiException
     * @throws Exception
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function handle(CartInterface $quote, array $addressData): string;
}
