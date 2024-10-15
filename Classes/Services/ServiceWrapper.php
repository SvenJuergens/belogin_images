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

use SvenJuergens\BeloginImages\Utility\ExtensionConfigUtility;
use TYPO3\CMS\Core\Page\PageRenderer;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use Symfony\Component\DependencyInjection\Attribute\Autoconfigure;
#[Autoconfigure(public: true)]
class ServiceWrapper
{
    protected mixed $settings;

    public function __construct()
    {
        $this->settings = ExtensionConfigUtility::getSettings();
    }

    /**
     * @throws \Exception
     */
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

    protected function showImages(): bool
    {
        if (empty($this->settings)) {
            return true;
        }
        if (isset($this->settings['IPmask']) && empty($this->settings['IPmask'])) {
            return true;
        }

        //check current IP
        if (isset($this->settings['IPmask'])
            && !empty($this->settings['IPmask'])
            && GeneralUtility::cmpIP(GeneralUtility::getIndpEnv('REMOTE_ADDR'), $this->settings['IPmask'])
        ) {
            return true;
        }
        return false;
    }

    /**
     * @throws \Exception
     */
    protected function getImageInfo(): array
    {
        return match ($this->settings['source']) {
            'google' => ChromeCastService::image($this->settings),
            'unsplash' => UnsplashService::image($this->settings),
            'folder' => FolderService::image($this->settings),
            'bing' => BingService::image($this->settings),
            default => [],
        };
    }
}
