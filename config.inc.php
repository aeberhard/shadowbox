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

	// Name des Addons und Pfade
	unset($rxa_shadowbox);
	$rxa_shadowbox['name'] = 'shadowbox';

	$rxa_shadowbox['rexversion'] = isset($REX['VERSION']) ? $REX['VERSION'] . $REX['SUBVERSION'] : $REX['version'] . $REX['subversion'];
	
	$REX['ADDON']['version'][$rxa_shadowbox['name']] = '1.1';
	$REX['ADDON']['author'][$rxa_shadowbox['name']] = 'Andreas Eberhard';

	$rxa_shadowbox['path'] = $REX['INCLUDE_PATH'].'/addons/'.$rxa_shadowbox['name'];
	$rxa_shadowbox['basedir'] = dirname(__FILE__);
	$rxa_shadowbox['lang_path'] = $REX['INCLUDE_PATH']. '/addons/'. $rxa_shadowbox['name'] .'/lang';
	$rxa_shadowbox['sourcedir'] = $REX['INCLUDE_PATH']. '/addons/'. $rxa_shadowbox['name'] .'/'. $rxa_shadowbox['name'];
	$rxa_shadowbox['meldung'] = '';
	
	$rxa_shadowbox['filesdir'] = $REX['HTDOCS_PATH'].'files/'.$rxa_shadowbox['name'];
	if ( in_array($rxa_shadowbox['rexversion'], array('42', '43')) ) {
		$rxa_shadowbox['filesdir'] = $REX['HTDOCS_PATH'].'files/addons/'.$rxa_shadowbox['name'];
	}
	$rxa_shadowbox['htdocsfilesdir'] = $rxa_shadowbox['filesdir'];

	// für Kompatibilität REDAXO 3.1, 3.2.x, 4.0.x
	include($rxa_shadowbox['basedir'] . '/functions/functions.compat.inc.php');		

/**
 * --------------------------------------------------------------------
 * Nur im Backend
 * --------------------------------------------------------------------
 */
	if (!$REX['GG']) {
		// Sprachobjekt anlegen
		$rxa_shadowbox['i18n'] = new i18n($REX['LANG'],$rxa_shadowbox['lang_path']);

		// Anlegen eines Navigationspunktes im REDAXO Hauptmenu
		$REX['ADDON']['page'][$rxa_shadowbox['name']] = $rxa_shadowbox['name'];
		// Namensgebung für den Navigationspunkt
		$REX['ADDON']['name'][$rxa_shadowbox['name']] = $rxa_shadowbox['i18n']->msg('menu_link');

		// Berechtigung für das Addon
		$REX['ADDON']['perm'][$rxa_shadowbox['name']] = $rxa_shadowbox['name'].'[]';
		// Berechtigung in die Benutzerverwaltung einfügen
		$REX['PERM'][] = $rxa_shadowbox['name'].'[]';		
	}

/**
 * --------------------------------------------------------------------
 * Outputfilter für das Frontend
 * --------------------------------------------------------------------
 */
	if ($REX['GG'])
	{
		rex_register_extension('OUTPUT_FILTER', 'shadowbox_opf');

		// Prüfen ob die aktuelle Kategorie mit der Auswahl übereinstimmt
		function shadowbox_check_cat($acat, $aart, $subcats, $shadowbox_cats)
		{

			// prüfen ob Kategorien ausgewählt
			if (!is_array($shadowbox_cats)) return false;

			// aktuelle Kategorie in den ausgewählten dabei?
			if (in_array($acat, $shadowbox_cats)) return true;

			// Prüfen ob Parent der aktuellen Kategorie ausgewählt wurde
			if ( ($acat > 0) and ($subcats == 1) )
			{
				$cat = OOCategory::getCategoryById($acat);
				while($cat = $cat->getParent())
				{
					if (in_array($cat->_id, $shadowbox_cats)) return true;
				}
			}

			// evtl. noch Root-Artikel prüfen
			if (strstr(implode('',$shadowbox_cats), 'r'))
			{
				if (in_array($aart.'r', $shadowbox_cats)) return true;
			}

			// ansonsten keine Ausgabe!
			return false;
		}

		// Output-Filter
		function shadowbox_opf($params)
		{
			global $REX, $REX_ARTICLE;
			global $rxa_shadowbox;

			// Für REDAXO < 4.2
			if (isset($REX_ARTICLE))
			{
				$REX['ARTICLE'] = $REX_ARTICLE;
			}
			
			$content = $params['subject'];
			
			if ( !strstr($content,'</head>') or !file_exists($rxa_shadowbox['path'].'/'.$rxa_shadowbox['name'].'.ini')
			 or ( strstr($content,'<script type="text/javascript" src="'.$rxa_shadowbox['htdocsfilesdir'].'/shadowbox-base.js"></script>') and strstr($content,'<link rel="stylesheet" href="'.$rxa_shadowbox['htdocsfilesdir'].'/shadowbox.js" type="text/css" media="screen" />') ) ) {
				return $content;
			}

			// Einstellungen aus ini-Datei laden
			if (($lines = file($rxa_shadowbox['path'].'/'.$rxa_shadowbox['name'].'.ini')) === FALSE) {
				return $content;
			} else {
				$va = explode(',', trim($lines[0]));
				$allcats = trim($va[0]);
				$subcats = trim($va[1]);
				$shadowbox_cats = array();
				$shadowbox_cats = unserialize(trim($lines[1]));
				$rxa_shadowbox['excludeids'] = unserialize(trim($lines[2]));
			}

			// aktuellen Artikel ermitteln
			$artid = isset($_GET['article_id']) ? $_GET['article_id']+0 : 0;
			if ($artid==0) {
				$artid = $REX['ARTICLE']->getValue('article_id')+0;
			}
			if ($artid==0) { $artid = $REX['START_ARTICLE_ID']; }

			if (!$artid) { return $content; }

			$article = OOArticle::getArticleById($artid);
			if (!$article) { return $content; }
			
			// Exclude ID?
			if (in_array($artid, explode(',', $rxa_shadowbox['excludeids']))) { return $content; }			

			// aktuelle Kategorie ermitteln
			if ( in_array($rxa_shadowbox['rexversion'], array('3.11')) ) {
				$acat = $article->getCategoryId();
			}
			if ( in_array($rxa_shadowbox['rexversion'], array('32', '40', '41', '42', '43')) ) {
				$cat = $article->getCategory();
				if ($cat) {
					$acat = $cat->getId();
				}
			}
			// Wenn keine Kategorie ermittelt wurde auf -1 setzen für Prüfung in shadowbox_check_cat, Prüfung auf Artikel im Root
			if (!isset($acat) or !$acat) { $acat = -1; }

			// Array anlegen falls keine Kategorien ausgewählt wurden
			if (!is_array($shadowbox_cats)){
				$shadowbox_cats = array();
			}

			// Code für shadowbox im head-Bereich ausgeben
			if ( ($allcats==1) or (shadowbox_check_cat($acat, $artid, $subcats, $shadowbox_cats) == true) )
			{
				$rxa_shadowbox['output'] = '	<!-- Addon Shadowbox '.$REX['ADDON']['version'][$rxa_shadowbox['name']].' -->'."\n";
				$rxa_shadowbox['output'] .= '	<script type="text/javascript" src="'.$rxa_shadowbox['htdocsfilesdir'].'/shadowbox-base.js"></script>'."\n";
				$rxa_shadowbox['output'] .= '	<script type="text/javascript" src="'.$rxa_shadowbox['htdocsfilesdir'].'/shadowbox.js"></script>'."\n";
				$rxa_shadowbox['output'] .= '	<script type="text/javascript">'."\n";
				$rxa_shadowbox['output'] .= '	Shadowbox.loadSkin("classic", "'.$rxa_shadowbox['htdocsfilesdir'].'/skin");'."\n";
				$rxa_shadowbox['output'] .= '	Shadowbox.loadLanguage("de-DE", "'.$rxa_shadowbox['htdocsfilesdir'].'/lang");'."\n";
//				$rxa_shadowbox['output'] .= '	Shadowbox.loadPlayer(["flv", "html", "iframe", "img", "qt", "swf", "wmp"], "files/shadowbox/player");'."\n";
				$rxa_shadowbox['output'] .= '	window.onload = function() {'."\n";
				$rxa_shadowbox['output'] .= '		var options = {'."\n";
				$rxa_shadowbox['output'] .= '			animSequence: "sync"'."\n";
				$rxa_shadowbox['output'] .= '		};'."\n";
				$rxa_shadowbox['output'] .= '		Shadowbox.init(options);'."\n";
				$rxa_shadowbox['output'] .= '	};'."\n";
				$rxa_shadowbox['output'] .= '	</script>'."\n";
				$content = str_replace('</head>', $rxa_shadowbox['output'].'</head>', $content);
			}
			return $content;
		}

	}
?>