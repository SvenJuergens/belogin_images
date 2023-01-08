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
class BingService
{
    /**
     * @throws \Exception
     */
    public static function image($settings): array
    {
        $imageData = [];
        $json = json_decode(
            file_get_contents(
                'https://www.bing.com/HPImageArchive.aspx?format=js&idx=0&n=8&pid=hp'
            ),
            true
        );

        if (is_array($json)) {
            $randomNumber = random_int(0, 8);
            $imageData = [
                'url' => 'https://www.bing.com/' . $json['images'][$randomNumber]['url'],
                'author' => $json['images'][$randomNumber]['copyrightonly']
            ];
        }
        return $imageData;
    }
}
