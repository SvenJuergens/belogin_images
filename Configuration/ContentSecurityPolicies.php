<?php

use SvenJuergens\BeloginImages\Utility\ExtensionConfigUtility;
use TYPO3\CMS\Core\Security\ContentSecurityPolicy\Directive;
use TYPO3\CMS\Core\Security\ContentSecurityPolicy\Mutation;
use TYPO3\CMS\Core\Security\ContentSecurityPolicy\MutationCollection;
use TYPO3\CMS\Core\Security\ContentSecurityPolicy\MutationMode;
use TYPO3\CMS\Core\Security\ContentSecurityPolicy\Scope;
use TYPO3\CMS\Core\Security\ContentSecurityPolicy\SourceScheme;
use TYPO3\CMS\Core\Security\ContentSecurityPolicy\UriValue;
use TYPO3\CMS\Core\Type\Map;

$source = ExtensionConfigUtility::getSettings('source');

$collection = [];

if ($source === 'google') {
    $collection = [
        new Mutation(MutationMode::Extend, Directive::ImgSrc, SourceScheme::data, new UriValue('https://*.googleusercontent.com')),
    ];
}
if ($source === 'bing') {
    $collection = [
        new Mutation(MutationMode::Extend, Directive::ImgSrc, SourceScheme::data, new UriValue('https://www.bing.com')),
    ];
}

return Map::fromEntries([
    Scope::backend(),
    new MutationCollection(...$collection),
]);
