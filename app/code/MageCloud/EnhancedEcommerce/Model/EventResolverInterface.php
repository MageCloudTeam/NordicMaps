<?php
/**
 * @author andy
 * @email andyworkbase@gmail.com
 * @team MageCloud
 * @package MageCloud_EnhancedEcommerce
 */
declare(strict_types=1);

namespace MageCloud\EnhancedEcommerce\Model;

/**
 * Interface EventResolverInterface
 * @package MageCloud\EnhancedEcommerce\Model;
 */
interface EventResolverInterface
{
    /**
     * @param array $eventArguments
     * @return string
     */
    public function resolve(array $eventArguments = []): string;
}
