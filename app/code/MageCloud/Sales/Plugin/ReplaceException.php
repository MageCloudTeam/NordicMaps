<?php
/**
 * Copyright (c) 2020. Volodymyr Hryvinskyi.  All rights reserved.
 * @author: <mailto:volodymyr@hryvinskyi.com>
 * @github: <https://github.com/hryvinskyi>
 */

declare(strict_types=1);

namespace MageCloud\Sales\Plugin;

use Closure;
use Magento\Sales\Api\Data\ShipmentCommentCreationInterface;
use Magento\Sales\Api\Data\ShipmentCreationArgumentsInterface;
use Magento\Sales\Exception\CouldNotShipException;
use Magento\Sales\Model\ShipOrder;

/**
 * Class ReplaceException
 */
class ReplaceException
{
    /**
     * @param ShipOrder $subject
     * @param Closure $process
     * @param $orderId
     * @param array $items
     * @param bool $notify
     * @param bool $appendComment
     * @param ShipmentCommentCreationInterface|null $comment
     * @param array $tracks
     * @param array $packages
     * @param ShipmentCreationArgumentsInterface|null $arguments
     */
    public function aroundExecute(
        ShipOrder $subject,
        Closure $process,
        $orderId,
        array $items = [],
        $notify = false,
        $appendComment = false,
        ShipmentCommentCreationInterface $comment = null,
        array $tracks = [],
        array $packages = [],
        ShipmentCreationArgumentsInterface $arguments = null
    ) {
        try {
            $return = $process($orderId, $items, $notify, $appendComment, $comment, $tracks, $packages, $arguments);
        } catch (\Exception $exception) {
            if ($exception->getPrevious()) {
                throw new CouldNotShipException(
                    __('Could not save a shipment. Error message: %1', $exception->getPrevious()->getMessage())
                );
            } else {
                throw new CouldNotShipException(__($exception->getMessage()));
            }
        }

        return $return;
    }
}