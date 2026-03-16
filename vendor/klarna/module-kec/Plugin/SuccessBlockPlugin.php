<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */
declare(strict_types=1);

namespace Klarna\Kec\Plugin;

use Klarna\Kec\Model\Session;
use Magento\Checkout\Block\Onepage\Success;

/**
 * @internal
 */
class SuccessBlockPlugin
{
    /**
     * @var Session
     */
    private Session $kecSession;

    /**
     * @param Session $kecSession
     * @codeCoverageIgnore
     */
    public function __construct(Session $kecSession)
    {
        $this->kecSession = $kecSession;
    }

    /**
     * Adding JS snippet to reload the minicart
     *
     * @param Success $success
     * @param string $result
     * @return string
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterGetAdditionalInfoHtml(Success $success, string $result)
    {
        if ($this->kecSession->entryExists()) {
            $result .= "
                <script>
                require(
                    [
                        'Magento_Customer/js/customer-data',
                    
                    ], function (customerData) {
                        customerData.reload(['cart'], true);
                    });
                </script>
            ";
            $this->kecSession->drop();
        }

        return $result;
    }
}
