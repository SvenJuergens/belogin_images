<?php

defined('TYPO3') || defined('TYPO3_MODE') || die('Access denied.');

/**
 * Here, the default login provider will be overridden
 * as we don't want to add a new kind of login but just
 * add background image and some info
 */
if (version_compare(\TYPO3\CMS\Core\Utility\VersionNumberUtility::getNumericTypo3Version(), '12.0.0', '>=')) {
    $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['backend']['loginProviders'][1433416747]['provider']
        = SvenJuergens\BeloginImages\LoginProvider\BeLoginExtender12::class;
} else {
    $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['backend']['loginProviders'][1433416747]['provider']
        = SvenJuergens\BeloginImages\LoginProvider\BeLoginExtender::class;
}
