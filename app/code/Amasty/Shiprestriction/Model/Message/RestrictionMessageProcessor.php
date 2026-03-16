<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Shipping Restrictions for Magento 2
 */

namespace Amasty\Shiprestriction\Model\Message;

use Amasty\Shiprestriction\Model\Rule;
use Magento\Quote\Model\Quote\Address\RateResult\AbstractResult;

class RestrictionMessageProcessor
{
    /**
     * @var array [ 'variable_name' => ['name' => 'name_key', 'data' => 'rate_data_key'] ]
     */
    private $variables = [];

    public function __construct(
        array $variables = []
    ) {
        $this->initialize($variables);
    }

    public function process(AbstractResult $rate, Rule $rule): string
    {
        $message = $this->execute($rule);
        foreach ($this->variables as $variable) {
            $message = str_replace(
                (string) $variable['name'],
                (string) $rate->getData($variable['data']),
                $message
            );
        }

        return $message;
    }

    private function execute(Rule $rule): string
    {
        return $rule->getShowRestrictionMessage() ? $rule->getCustomRestrictionMessage() : '';
    }

    private function initialize(array $variables): void
    {
        foreach ($variables as $name => $data) {
            if (!isset($data['name'], $data['data'])) {
                throw new \LogicException('Message variable data or name is missing');
            }
            $this->variables[$name] = $data;
        }
    }
}
