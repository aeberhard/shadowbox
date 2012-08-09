<?php
/**
 * --------------------------------------------------------------------
 *
 * Redaxo Addon: Shadowbox
 * Version: 1.1, 11.12.2009
 *
 * Autor: Andreas Eberhard, andreas.eberhard@gmail.com
 *        http://rex.andreaseberhard.de
 *
 * Verwendet wird das Script von Michael J. I. Jackson
 * http://mjijackson.com/shadowbox/
 *
 * --------------------------------------------------------------------
 */

	include('config.inc.php');
	if (!isset($rxa_shadowbox['name'])) {
		echo '<font color="#cc0000"><strong>Fehler! Eventuell wurde die Datei config.inc.php nicht gefunden!</strong></font>';
		return;
	}
		
	echo $rxa_shadowbox['i18n']->msg('text_help_title', $REX['ADDON']['version'][$rxa_shadowbox['name']]);
	$i=1;
	while ($rxa_shadowbox['i18n']->msg('text_help_'.$i)<>'[translate:text_help_'.$i.']') {
		echo $rxa_shadowbox['i18n']->msg('text_help_'.$i);
		$i++;
		if ($i>10) { break; }
	}
?>
