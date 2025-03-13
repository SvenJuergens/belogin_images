<?php

declare(strict_types=1);

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
    /**
     * @throws \JsonException
     */
    public static function image($settings): array
    {
        try {
            $json = json_decode(file_get_contents(
                GeneralUtility::getFileAbsFileName(
                    'EXT:belogin_images/Resources/Private/chromecast-json/backgrounds.json'
                )
            ), true, 512, JSON_THROW_ON_ERROR);
            $json2 = json_decode(file_get_contents(
                GeneralUtility::getFileAbsFileName(
                    'EXT:belogin_images/Resources/Private/chromecast-json/images.json'
                )
            ), true, 512, JSON_THROW_ON_ERROR);
        } catch (\Exception $e) {
            return [];
        }


        $json = array_merge($json, $json2);
        if (count($json) > 0) {
            $randomNumber = rand(0, count($json));
            $imageData = [
                'url' => $json[$randomNumber]['url'],
                'author' => $json[$randomNumber]['author'] ?? ($json[$randomNumber]['photographer'] ?? ''),
            ];
        } else {
            $imageData = [
                'url' => 'https://lh5.googleusercontent.com/-Hn2QgYPEDxo/Tg1bUgAlTfI/AAAAAAAAAI0/R33ZpN3IaJ8/s2560/061012-1078-PelicanCove.jpg',
                'author' => 'Patrick Smith',
            ];
        }
        return $imageData;
    }
}
