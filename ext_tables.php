<?php
if (!defined('TYPO3_MODE')) {
	die ('Access denied.');
}

Tx_Extbase_Utility_Extension::registerPlugin(
	$_EXTKEY,
	'Pi1',
	'Forge Teams'
);

t3lib_extMgm::addStaticFile($_EXTKEY, 'Configuration/TypoScript', 'Forge Integration');

t3lib_extMgm::addLLrefForTCAdescr('tx_t3o_redmine', 'EXT:t3o_redmine/Resources/Private/Language/locallang_csh_tx_t3o_redmine.xlf');

/**
 * Add flexform configuration
 */
$pluginName = 'pi1';
$pluginSignature = str_replace('_', '', $_EXTKEY) . '_' . $pluginName;
$TCA['tt_content']['types']['list']['subtypes_addlist'][$pluginSignature] = 'pi_flexform';
t3lib_extMgm::addPiFlexFormValue(
	$pluginSignature,
	'FILE:EXT:' . $_EXTKEY . '/Configuration/FlexForms/flexform_' . $pluginName . '.xml'
);
