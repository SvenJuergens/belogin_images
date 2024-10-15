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

namespace SvenJuergens\BeloginImages\EventListener;

use SvenJuergens\BeloginImages\Services\ServiceWrapper;
use TYPO3\CMS\Backend\LoginProvider\Event\ModifyPageLayoutOnLoginProviderSelectionEvent;
use TYPO3\CMS\Core\Attribute\AsEventListener;
use TYPO3\CMS\Core\Page\PageRenderer;

#[AsEventListener(
    identifier: 'belogin_images/extend-backend-login',
)]
final readonly class ModifyPageLayoutOnLoginProviderSelectionEventListener
{
    public function __construct(
        private PageRenderer $pageRenderer,
        private ServiceWrapper $serviceWrapper,
    ) {}

    /**
     * @throws \Exception
     */
    public function __invoke(ModifyPageLayoutOnLoginProviderSelectionEvent $event): void
    {
        $this->serviceWrapper->main($this->pageRenderer);
    }

}
