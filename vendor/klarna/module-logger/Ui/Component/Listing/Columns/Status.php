<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */
declare(strict_types=1);

namespace Klarna\Logger\Ui\Component\Listing\Columns;

use Magento\Ui\Component\Listing\Columns\Column;

/**
 * @internal
 */
class Status extends Column
{

    public const HTTP_NOT_FOUND = 404;
    public const HTTP_NO_CONTENT = 204;
    public const HTTP_OK = 200;
    public const HTTP_CREATED = 201;
    public const HTTP_INTERNAL_SERVER_ERROR = 500;

    /**
     * The status column will be rendered according to the http status response code.
     *
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        if (!isset($dataSource['data']['items'])) {
            return $dataSource;
        }

        foreach ($dataSource['data']['items'] as &$item) {
            switch ($item['status']) {
                case self::HTTP_OK:
                case self::HTTP_CREATED:
                case self::HTTP_NO_CONTENT:
                    $class = 'notice';
                    break;
                case self::HTTP_NOT_FOUND:
                case self::HTTP_INTERNAL_SERVER_ERROR:
                    $class = 'critical';
                    break;
                default:
                    $class = 'minor';
                    break;
            }
            $item['status'] = '<span class="grid-severity-' . $class . '">' . $item['status'] . '</span>';
        }

        return $dataSource;
    }
}
