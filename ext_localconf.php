<?php

defined('TYPO3') || die('Access denied.');

/**
 * Here, the default login provider will be overridden
 * as we don't want to add a new kind of login but just
 * add background image and some info
 */
$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['backend']['loginProviders'][1433416747]['provider']
    = SvenJuergens\BeloginImages\LoginProvider\BeLoginExtender::class;
