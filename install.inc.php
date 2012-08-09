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

	if (!function_exists('shadowbox_copydir')) {
	function shadowbox_copydir($source, $target)
	{
		if (is_dir($source))
		{
			@mkdir($target);
			$d = dir( $source );
			while (FALSE !== ($entry = $d->read()))
			{
				if ($entry == '.' || $entry == '..')
				{
					continue;
				}
				$Entry = $source . '/' . $entry;
				if ( is_dir( $Entry ) )
				{
					shadowbox_copydir( $Entry, $target . '/' . $entry );
					continue;
				}
				copy( $Entry, $target . '/' . $entry );
			}
			$d->close();
		}
		else
		{
			copy( $source, $target );
		}
	}
	} // End function_exists 	
	
	if (!function_exists("is__writable")) {
	function is__writable($path)
	{
		if ($path{strlen($path)-1}=='/') // recursively return a temporary file path
			return is__writable($path.uniqid(mt_rand()).'.tmp');
		else if (is_dir($path))
			return is__writable($path.'/'.uniqid(mt_rand()).'.tmp');
		// check tmp file for read/write capabilities
		$rm = file_exists($path);
		$f = @fopen($path, 'a');
		if ($f===false)
			return false;
		fclose($f);
		if (!$rm)
			unlink($path);
		return true;
	}
	} // End function_exists 	

	unset($rxa_shadowbox);
	include('config.inc.php');

	if (!isset($rxa_shadowbox['name'])) {
		echo '<font color="#cc0000"><strong>Fehler! Eventuell wurde die Datei config.inc.php nicht gefunden!</strong></font>';
		$REX['ADDON']['install'][$rxa_shadowbox['name']] = 0;
		return;
	}

	// Gültige REDAXO-Version abfragen
	if ( !in_array($rxa_shadowbox['rexversion'], array('3.11', '32', '40', '41', '42', '43')) ) {
		echo '<font color="#cc0000"><strong>Fehler! Ung&uuml;ltige REDAXO-Version - '.$rxa_shadowbox['rexversion'].'</strong></font>';
		$REX['ADDON']['installmsg'][$rxa_shadowbox['name']] = '<br /><br /><font color="#cc0000"><strong>Fehler! Ung&uuml;ltige REDAXO-Version - '.$rxa_shadowbox['rexversion'].'</strong></font>';
		$REX['ADDON']['install'][$rxa_shadowbox['name']] = 0;
		return;
	}

	// Schreibrechte für ini-Datei setzen
	@chmod($rxa_shadowbox['basedir'] . '/'. $rxa_shadowbox['name'] . '.ini', 0755);
	
	// Verzeichnis files/shadowbox anlegen
	if ( !@is_dir($rxa_shadowbox['filesdir']) ) {
		if ( !@mkdir($rxa_shadowbox['filesdir']) ) {
			$rxa_shadowbox['meldung'] .= $rxa_shadowbox['i18n']->msg('error_createdir', $rxa_shadowbox['filesdir']);
		}
	}
	@chmod($rxa_shadowbox['filesdir'], 0755);
	if (!is__writable($rxa_shadowbox['filesdir'].'/')) {
		$rxa_shadowbox['meldung'] .= $rxa_shadowbox['i18n']->msg('error_writedir', $rxa_shadowbox['filesdir']);
	}	
	
	// Dateien ins Verzeichnis files/shadowbox kopieren
	shadowbox_copydir($rxa_shadowbox['sourcedir'], $rxa_shadowbox['filesdir']);

	// Evtl Ausgabe einer Meldung
	// $rxa_shadowbox['meldung'] = 'Das Addon wurde nicht installiert, weil...';
	if ( $rxa_shadowbox['meldung']<>'' ) {
		$REX['ADDON']['installmsg'][$rxa_shadowbox['name']] = '<br /><br />'.$rxa_shadowbox['meldung'].'<br /><br />';
		$REX['ADDON']['install'][$rxa_shadowbox['name']] = 0;
	} else {
	// Installation erfolgreich
		$REX['ADDON']['install'][$rxa_shadowbox['name']] = 1;
	}
?>