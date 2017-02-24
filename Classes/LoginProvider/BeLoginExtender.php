<?php

namespace SvenJuergens\BeloginImages\LoginProvider;

/**
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */

use TYPO3\CMS\Backend\Controller\LoginController;
use TYPO3\CMS\Backend\LoginProvider\UsernamePasswordLoginProvider;
use TYPO3\CMS\Core\Page\PageRenderer;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Fluid\View\StandaloneView;

class BeLoginExtender extends UsernamePasswordLoginProvider
{
    /**
     * @param StandaloneView $view
     * @param PageRenderer $pageRenderer
     * @param LoginController $loginController
     */
    public function render(StandaloneView $view, PageRenderer $pageRenderer, LoginController $loginController)
    {
        parent::render($view, $pageRenderer, $loginController);

        if ($this->showImages() === false) {
            return;
        }
        $json = json_decode(
            GeneralUtility::getUrl(
                GeneralUtility::getFileAbsFileName(
                    'EXT:belogin_images/Resources/Private/Libraries/chromecast-backgrounds/backgrounds.json'
                )
            ),
            true
        );
        if (is_array($json)) {
            $backgroundImage = $json[rand(0, count($json))]['url'];
        } else {
            $backgroundImage = 'https://lh3.googleusercontent.com/nBVYnwmEQH9yRRE6CDqjaZ36-vV_yMolb4kPer0Dxkfuh529OFsQ=s1280-w1280-h720-p-k-no-nd-mv';
        }
        $pageRenderer->addCssInlineBlock('beloginimages', '
            @media (min-width: 768px){
                .typo3-login-carousel-control.right,
                .typo3-login-carousel-control.left,
                .panel-login { border: 0; }
                .typo3-login { background-image: url("' . $backgroundImage . '"); }
            }
        ');
    }

    public function showImages()
    {
        $settings = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['belogin_images']);
        if (empty($settings)) {
            return true;
        }
        if (isset($settings['IPmask']) && empty($settings['IPmask'])) {
            return true;
        }

        if (isset($settings['IPmask']) && !empty($settings['IPmask'])) {
            //check current IP
            if (GeneralUtility::cmpIP(GeneralUtility::getIndpEnv('REMOTE_ADDR'), $settings['IPmask'])) {
                return true;
            }
        }
        return false;
    }
}

