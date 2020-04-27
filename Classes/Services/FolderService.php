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

class FolderService
{
    public static function image($settings)
    {
        $imageData = [];
        if ($settings['folder']) {
            $absPath = GeneralUtility::resolveBackPath(
                $settings['folder']
            );
            // Get rotation folder:
            $dir = GeneralUtility::getFileAbsFileName($absPath);

            if ($dir && @is_dir($dir)) {
                // Get files for rotation into array:
                $files = GeneralUtility::getFilesInDir($dir, 'png,jpg');
                // Pick random file:
                mt_srand((float)microtime() * 10000000);
                $rand = array_rand($files, 1);
                $imageData = [
                    'url' => '/' . htmlspecialchars($settings['folder']) . $files[$rand]
                ];
            }
        }
        return $imageData;
    }
}
