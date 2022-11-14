<?php

declare(strict_types=1);

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

use SvenJuergens\BeloginImages\Services\BingService;
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
                .typo3-login:before { 
                    background-image: url("' . $imageData['url'] . '") !important;
                    content: "";
                     position: absolute;
                     top: 0;
                     right: 0;
                     bottom: 0;
                     left: 0;
                     z-index: 1;
                     animation: 2s ease 0s normal forwards 1 fadein;
                     animation-iteration-count: 1;
                     background-size: cover;
                }
                .card, .panel {
                    background-color: #ffffffb8;
                    transition: background-color 300ms linear;
                }
                .card:hover, .panel:hover {background-color: #fff}
                @keyframes fadein{
                    0% { opacity:0; }
                    100% { opacity:1; }
                }
        ';
        if (isset($imageData['author']) && !empty($imageData['author'])) {
            $imageCSS[] = '
            .typo3-login:after{
                    content: " ' . $imageData['author'] . ' ";
                    position: absolute;
                    left:0;
                    bottom:20px;
                    color: #000;
                    font-size:18px;
                    background-color:rgba(255,255,255,0.4);
                    padding:2px 0 2px 30px;
                    width:100%;
                    z-index: 2;
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

    public function showImages(): bool
    {
        $settings = $this->getSettings();
        if (empty($settings)) {
            return true;
        }
        if (isset($settings['IPmask']) && empty($settings['IPmask'])) {
            return true;
        }

        //check current IP
        if (isset($settings['IPmask'])
            && !empty($settings['IPmask'])
            && GeneralUtility::cmpIP(GeneralUtility::getIndpEnv('REMOTE_ADDR'), $settings['IPmask'])
        ) {
            return true;
        }
        return false;
    }

    public function getImageInfo(): array
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
            case 'bing':
                $imageData = BingService::image($this->settings);
                break;
            default:
                $imageData = [];
                break;
        }
        return $imageData;
    }

    /**
     * @return mixed
     */
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
