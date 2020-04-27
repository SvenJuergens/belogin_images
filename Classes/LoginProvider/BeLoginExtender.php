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

use SvenJuergens\BeloginImages\Services\ChromeCastService;
use SvenJuergens\BeloginImages\Services\FolderService;
use SvenJuergens\BeloginImages\Services\UnsplashService;
use TYPO3\CMS\Backend\Controller\LoginController;
use TYPO3\CMS\Backend\LoginProvider\UsernamePasswordLoginProvider;
use TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationExtensionNotConfiguredException;
use TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationPathDoesNotExistException;
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;
use TYPO3\CMS\Core\Page\PageRenderer;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Fluid\View\StandaloneView;

class BeLoginExtender extends UsernamePasswordLoginProvider
{
    public $settings = [];

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
        $imageData = $this->getImageInfo();

        $imageCSS[] = '
                .typo3-login-carousel-control.right,
                .typo3-login-carousel-control.left,
                .panel-login { border: 0; }
                .typo3-login { background-image: url("' . $imageData['url'] . '") !important }
        ';
        if (isset($imageData['author']) && !empty($imageData['author'])) {
            $imageCSS[] = '
            .typo3-login:after{
                    content: " ' . $imageData['author']  . ' ";
                    position: absolute;
                    left:0;
                    bottom:20px;
                    color: #000;
                    font-size:18px;
                    background-color:rgba(255,255,255,0.4);
                    padding:2px 0 2px 30px;
                    width:100%;
                }
            ';
        }

        $pageRenderer->addCssInlineBlock(
            'beloginimages',
            '
            @media (min-width: 768px){' .
                implode('', $imageCSS)
            . '
            }
            '
        );
    }

    public function showImages()
    {
        $settings = $this->getSettings();
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

    public function getImageInfo()
    {
        switch ($this->settings['source']) {
            case 'google':
                $imageData =  ChromeCastService::image($this->settings);
                break;
            case 'unsplash':
                $imageData =  UnsplashService::image($this->settings);
                break;
            case 'folder':
                $imageData = FolderService::image($this->settings);
                break;
            default:
                $imageData = [];
                break;
        }
        return $imageData;
    }

    public function getSettings()
    {
        if (empty($this->settings)) {
            try {
                $this->settings = GeneralUtility::makeInstance(ExtensionConfiguration::class)
                    ->get('belogin_images');
            } catch (ExtensionConfigurationExtensionNotConfiguredException $e) {
                // do nothing
            } catch (ExtensionConfigurationPathDoesNotExistException $e) {
                // do nothing
            }
        }
        return $this->settings;
    }
}
