<?php

namespace SvenJuergens\BeloginImages\Utility;

use TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationExtensionNotConfiguredException;
use TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationPathDoesNotExistException;
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class ExtensionConfigUtility
{
    /**
     * @param string $settingsKey
     * @return mixed
     */
    public static function getSettings(string $settingsKey = ''): mixed
    {
        try {
            return GeneralUtility::makeInstance(ExtensionConfiguration::class)
                ->get('belogin_images', $settingsKey);
        } catch (ExtensionConfigurationExtensionNotConfiguredException|ExtensionConfigurationPathDoesNotExistException $e) {
            // do nothing
        }
    }
}
