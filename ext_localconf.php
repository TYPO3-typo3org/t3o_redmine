<?php
if (!defined('TYPO3_MODE')) {
    die ('Access denied.');
}

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
    'T3o.' . $_EXTKEY,
    'Pi1',
    array(
        'Team' => 'index',

    ),
    // non-cacheable actions
    array()
);

?>
