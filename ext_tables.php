<?php
if (!defined('TYPO3_MODE')) {
    die ('Access denied.');
}

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
    $_EXTKEY,
    'Pi1',
    'Forge Teams'
);

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile($_EXTKEY, 'Configuration/TypoScript', 'Forge Integration');

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addLLrefForTCAdescr('tx_t3o_redmine', 'EXT:t3o_redmine/Resources/Private/Language/locallang_csh_tx_t3o_redmine.xlf');

/**
 * Add flexform configuration
 */
$pluginName = 'pi1';
$pluginSignature = str_replace('_', '', $_EXTKEY) . '_' . $pluginName;
$TCA['tt_content']['types']['list']['subtypes_addlist'][$pluginSignature] = 'pi_flexform';
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPiFlexFormValue(
    $pluginSignature,
    'FILE:EXT:' . $_EXTKEY . '/Configuration/FlexForms/flexform_' . $pluginName . '.xml'
);
