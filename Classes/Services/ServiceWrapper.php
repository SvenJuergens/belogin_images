<?php

declare(strict_types=1);

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
namespace SvenJuergens\BeloginImages\Services;

use TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationExtensionNotConfiguredException;
use TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationPathDoesNotExistException;
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;
use TYPO3\CMS\Core\Page\PageRenderer;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class ServiceWrapper
{
    public $settings = [];

    public function main(PageRenderer $pageRenderer): void
    {
        if ($this->showImages() === false) {
            return;
        }
        $imageData = $this->getImageInfo();

        $imageCSS[] = '
                :root {
                  --beloginImagesBackgroundImage: url( ' . ($imageData['url'] ?? '') . ' ) !important;
                  --beloginImagesBackgroundImageCreator: "' . ($imageData['author'] ?? '') . '";
                }
        ';
        if (empty($imageData['author'])) {
            $imageCSS[] = '
                .typo3-login:after { display:none !important;}
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
        $pageRenderer->addCssFile(
            GeneralUtility::getFileAbsFileName('EXT:belogin_images/Resources/Public/Css/belogin_images.css'),
            'stylesheet'
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
