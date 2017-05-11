<?php
namespace SvenJuergens\BeloginImages\Services;

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
use TYPO3\CMS\Core\Utility\GeneralUtility;

class ChromeCastService
{
     public static function image($settings){
        $json = json_decode(
            GeneralUtility::getUrl(
                GeneralUtility::getFileAbsFileName(
                    'EXT:belogin_images/Resources/Private/Libraries/chromecast-backgrounds/backgrounds.json'
                )
            ),
            true
        );
        if (is_array($json)) {
            $randomNumber = rand(0, count($json));
            $imageData = [
                'url' => $json[$randomNumber]['url'],
                'author' => $json[$randomNumber]['author']
            ];
        } else {
            $imageData = [
                'url' => 'https://lh4.googleusercontent.com/-7EJI2_bMWrg/U0_6WXfnu0I/AAAAAAAA2IA/qnv2qDY374E/s1920-w1920-h1080-c/388A4957.jpg',
                'author' => 'Leo Deegan'
            ];
        }
        return $imageData;
    }
}