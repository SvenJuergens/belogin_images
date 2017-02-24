<?php

/**
 * Here, the default login provider will be overridden
 * as we don't want to add a new kind of login but just
 * add a captcha to the username and password login.
 */
$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['backend']['loginProviders'][1433416747]['provider']
    = SvenJuergens\BeloginImages\LoginProvider\BeLoginExtender::class;
