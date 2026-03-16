<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) 2023 Amasty (https://www.amasty.com)
 * @package GDPR Base for Magento 2
 */

namespace Amasty\Gdpr\Setup\Patch\Data;

use Amasty\Gdpr\Model\ThirdParty\ModuleChecker;
use Amasty\GdprFaqSampleData\Setup\SampleData\Installer;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;

class InstallSampleData implements DataPatchInterface
{
    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;

    /**
     * @var ModuleChecker
     */
    private $moduleChecker;

    public function __construct(
        ObjectManagerInterface $objectManager,
        ModuleChecker $moduleChecker
    ) {
        $this->objectManager = $objectManager;
        $this->moduleChecker = $moduleChecker;
    }

    public function apply()
    {
        if ($this->moduleChecker->isAmastyGdprFaqSampleDataEnabled()
            && $this->moduleChecker->isAmastyFaqEnabled()
        ) {
            $this->getSampleDataInstaller()->install();
        }
    }

    public static function getDependencies()
    {
        return [];
    }

    public function getAliases()
    {
        return [];
    }

    private function getSampleDataInstaller(): Installer
    {
        return $this->objectManager->get(Installer::class);
    }
}
