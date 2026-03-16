<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */
declare(strict_types=1);

namespace Klarna\Kp\Setup\Patch\Data;

use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Framework\Setup\Patch\PatchVersionInterface;
use Magento\Sales\Setup\SalesSetup;

/**
 * @internal
 */
class RemoveHtmlTag implements DataPatchInterface, PatchVersionInterface
{
    /**
     * @var SalesSetup
     */
    private $salesSetup;

    /**
     * @param SalesSetup $salesSetup
     * @codeCoverageIgnore
     */
    public function __construct(SalesSetup $salesSetup)
    {
        $this->salesSetup = $salesSetup;
    }

    /**
     * @inheritDoc
     */
    public function apply()
    {
        $this->salesSetup->getConnection()->startSetup();
        $this->removeStrongHtmlTag();
        $this->salesSetup->getConnection()->endSetup();

        return $this;
    }

    /**
     * @inheritDoc
     */
    public static function getDependencies()
    {
        return [];
    }

    /**
     * @inheritDoc
     */
    public function getAliases()
    {
        return [];
    }

    /**
     * @inheritDoc
     */
    public static function getVersion()
    {
        return '5.4.5';
    }

    /**
     * Remove the html tag 'strong' from the additional information of the payments
     */
    private function removeStrongHtmlTag()
    {
        $values = [
            '<strong>',
            '<\/strong>'
        ];
        foreach ($values as $value) {
            $manipulation = new \Zend_Db_Expr("replace(`additional_information`, '$value', '')");
            $this->salesSetup->getConnection()
                ->update(
                    $this->salesSetup->getTable('sales_order_payment'),
                    ['additional_information' => $manipulation],
                    "`method` = 'klarna_kp'"
                );
        }
    }
}
