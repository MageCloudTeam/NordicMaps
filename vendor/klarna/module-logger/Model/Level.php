<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */
declare(strict_types=1);

namespace Klarna\Logger\Model;

/**
 * Providing the logging levels
 *
 * @internal
 */
class Level
{

    public const DEBUG = 100;
    public const INFO = 200;
    public const NOTICE = 250;
    public const WARNING = 300;
    public const ERROR = 400;
    public const CRITICAL = 500;
    public const ALERT = 550;
    public const EMERGENCY = 600;

    /**
     * Get back the debug level
     *
     * @return int
     */
    public function getDebugLevel()
    {
        return self::DEBUG;
    }

    /**
     * Get back the info level
     *
     * @return int
     */
    public function getInfoLevel()
    {
        return self::INFO;
    }

    /**
     * Get back the notice level
     *
     * @return int
     */
    public function getNoticeLevel()
    {
        return self::NOTICE;
    }

    /**
     * Get back the warning level
     *
     * @return int
     */
    public function getWarningLevel()
    {
        return self::WARNING;
    }

    /**
     * Get back the error level
     *
     * @return int
     */
    public function getErrorLevel()
    {
        return self::ERROR;
    }

    /**
     * Get back the critical level
     *
     * @return int
     */
    public function getCriticalLevel()
    {
        return self::CRITICAL;
    }

    /**
     * Get back the alert level
     *
     * @return int
     */
    public function getAlertLevel()
    {
        return self::ALERT;
    }

    /**
     * Get back the emergency level
     *
     * @return int
     */
    public function getEmergencyLevel()
    {
        return self::EMERGENCY;
    }

    /**
     * Getting back a array of levels
     *
     * @return array
     */
    public function getLevelNames()
    {
        return [
            $this->getDebugLevel() => 'DEBUG',
            $this->getInfoLevel() => 'INFO',
            $this->getNoticeLevel() => 'NOTICE',
            $this->getWarningLevel() => 'WARNING',
            $this->getErrorLevel() => 'ERROR',
            $this->getCriticalLevel() => 'CRITICAL',
            $this->getAlertLevel() => 'ALERT',
            $this->getEmergencyLevel() => 'EMERGENCY'
        ];
    }
}
