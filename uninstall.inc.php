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

	if (!function_exists('shadowbox_removedir')) {
	function shadowbox_removedir( $dir ) {
		$handle = opendir( $dir );
		while ( $file = readdir ( $handle ) ) {
			if ( eregi( "^\.{1,2}$", $file ) ) continue;
			if ( is_dir( $dir."/".$file ) ) {
				shadowbox_removedir($dir."/".$file);
				rmdir ($dir."/".$file);
			} else {
				unlink ("$dir/$file");
			}
		}
		closedir ($handle);
	}
	} // End function_exists 

	unset($rxa_shadowbox); 
	include('config.inc.php');
	
	if (!isset($rxa_shadowbox['name'])) {
		echo '<font color="#cc0000"><strong>Fehler! Eventuell wurde die Datei config.inc.php nicht gefunden!</strong></font>';
		return;
	}

	// Dateien aus dem Ordner files/shadowbox löschen
	if ( !in_array($rxa_shadowbox['rexversion'], array('42', '43')) ) {
		shadowbox_removedir($rxa_shadowbox['filesdir']);
		@rmdir($rxa_shadowbox['filesdir']);
	}

	// Evtl Ausgabe einer Meldung
	// De-Installation nicht erfolgreich
	if ( $rxa_shadowbox['meldung']<>'' ) {
		$REX['ADDON']['installmsg'][$rxa_shadowbox['name']] = '<br /><br />'.$rxa_shadowbox['meldung'].'<br /><br />';
		$REX['ADDON']['install'][$rxa_shadowbox['name']] = 1;
	// De-Installation erfolgreich
	} else {
		$REX['ADDON']['install'][$rxa_shadowbox['name']] = 0;
	}
?>