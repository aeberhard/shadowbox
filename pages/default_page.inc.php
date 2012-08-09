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

/**
 * Options für die Selectbox aufbauen
 */
function add_cat_options(&$rxa_addon, &$select, &$cat, &$cat_ids, $groupName = '', $nbsp = '')
{
	global $REX;
	
	if (empty($cat)) {
		return;
	}

	$cat_ids[] = $cat->getId();
	if( $REX['USER']->isValueOf("rights","admin[]") || $REX['USER']->isValueOf("rights","csw[0]") || $REX['USER']->isValueOf("rights","csr[".$cat->getId()."]") || $REX['USER']->isValueOf("rights","csw[".$cat->getId()."]") )
	{
		$sed = '';
		if (in_array($cat->getId(), $rxa_addon['cats']))
		{
			$sed  = ' selected="selected"';
		}
		$select .= '<option value="'.$cat->getId().'"'.$sed.'>' . $nbsp . $cat->getName();
		$select .= '</option>'."\n";

		$rxa_addon['catselectcount']+=1;

		$childs = $cat->getChildren();
		if (is_array($childs))
		{
			$nbsp = $nbsp.'&nbsp;&nbsp;&nbsp;&nbsp;';
			foreach ( $childs as $child)
			{
				add_cat_options($rxa_addon, $select, $child, $cat_ids, $cat->getName(), $nbsp);
			}
		}
	}
}

/**
 * Root-Artikel in Selectbox übernehmen
 */
function add_rootart_options($rxa_addon, &$select, $clang)
{
	$artroot = OOArticle::getRootArticles(false, $clang);
	if (count($artroot) > 0) {

		$select .= '<optgroup label="'.$rxa_addon['i18n']->msg('text_rootarticles').'">'."\n";

		foreach (OOArticle::getRootArticles(false, $clang) as $artroot)
		{
			$sed = '';
			if (in_array($artroot->getId().'r', $rxa_addon['cats']))
			{
				$sed  = ' selected="selected"';
			}
			$select .= '<option value="'.$artroot->getId().'r'.'"'.$sed.'>' . $artroot->getName();
			$select .= '</option>'."\n";
		}

		$select .= '</optgroup>'."\n";
	}
}

/**
 * Auswahl speichern
 */
	if ( isset($_POST['function']) and ($_POST['function']=='save') ) {
		if (isset($_POST['allcats'])) {
			$allcats = $_POST['allcats'];
		} else {
			$allcats = '';
		}
		if (trim($allcats=='')) $allcats = 0;

		if (isset($_POST['subcats'])) {
			$subcats = $_POST['subcats'];
		} else {
			$subcats = '';
		}
		if (trim($subcats=='')) $subcats = 0;

		$line = $allcats.','.$subcats."\n";
		if (isset($_POST['category_select'])) {
			$line .= serialize($_POST['category_select'])."\n";
		} else {
			$line .= "N;\n";
		}
		if (isset($_POST['excludeids'])) {
			$line .= serialize($_POST['excludeids'])."\n";
		} else {
			$line .= "N;\n";
		}		

		if (($fh = fopen($rxa_shadowbox['path'].'/'.$rxa_shadowbox['name'].'.ini', 'w')) === FALSE) {
			$rxa_shadowbox['meldung'] = $rxa_shadowbox['i18n']->msg('error_save',$rxa_shadowbox['path'].'/'.$rxa_shadowbox['name'].'.ini');
		} else {
			@fwrite($fh, $line);
			@fclose($fh);	
			$rxa_shadowbox['meldung'] = $rxa_shadowbox['i18n']->msg('msg_saved');
		}
	}

/**
 * Auswahl laden
 */
	if (($lines = file($rxa_shadowbox['path'].'/'.$rxa_shadowbox['name'].'.ini')) === FALSE) {
		$rxa_shadowbox['meldung'] = $rxa_shadowbox['i18n']->msg('error_read',$rxa_shadowbox['path'].'/'.$rxa_shadowbox['name'].'.ini');
	} else {
		$va = explode(',', trim($lines[0]));
		$allcats = trim($va[0]);
		$subcats = trim($va[1]);
		$rxa_shadowbox['cats'] = unserialize(trim($lines[1]));
		$rxa_shadowbox['excludeids'] = unserialize(trim($lines[2]));
	}
	if (!is_array($rxa_shadowbox['cats'])){
		$rxa_shadowbox['cats'] = array();
	}
	
/**
 * Select-Klasse erstellen und mit "Leben" füllen
 */
	$cat_ids[] = '';
	$cat='0';
	$rxa_shadowbox['catselectcount'] = 0;

	$select_cats = "\n";
	if ($cats = OOCategory::getRootCategories())
	{
		foreach( $cats as $cat)
		{
			add_cat_options($rxa_shadowbox, $select_cats, $cat, $cat_ids);
		}
	}
	//Artikel aus dem Root ebenso in die Auswahl (selectbox) übernehmen
	if (!isset($clang))
	{
		$clang = rex_request('clang', 'integer');
	}	
	add_rootart_options($rxa_shadowbox, $select_cats, $clang);

	$selsize = $rxa_shadowbox['catselectcount'] / 3;
	($selsize <= 15) ? $selsize = 15 : ( ($selsize >= 25) ? $selsize = 25 : $selsize = $selsize );

	$select_cats = '<select name="category_select[]" size="'.$selsize.'" id="id_category_select" multiple="multiple">'."\n" . $select_cats;
	$select_cats .= '</select>'."\n";
?>

<?php
	if ($rxa_shadowbox['meldung']<>'') echo rex_info($rxa_shadowbox['meldung']);
?>

<?php echo $rxa_compat['backendprefix']; ?>

<div class="rex-addon-output">
<h2 class="rex-hl2"><?php echo $rxa_shadowbox['i18n']->msg('menu_settings'); ?></h2>

<div class="rex-form">

<form action="index.php?page=<?php echo $rxa_shadowbox['name']; ?>" method="post">
<fieldset class="rex-form-col-1">
<div class="rex-form-wrapper">

<input type="hidden" name="function" value="save" />

	<div class="rex-form-row">
	<div class="rex-addon-content">
		<p>
<?php
	echo $rxa_shadowbox['i18n']->msg('text_settings_intro');
?>
		</p>
	</div>
	</div>	

	<div class="rex-form-row">
		<p class="rex-form-col-a rex-form-checkbox rex-form-label-right">
			<input class="rex-form-checkbox" type="checkbox" id="allcats" name="allcats" value="1" <?php if ($allcats == "1") echo "checked"; ?> />
			<label for="allcats"><?php echo $rxa_shadowbox['i18n']->msg('text_settings_allcats'); ?></label>
		</p>
	</div>
        
	<div class="rex-form-row">
		<p class="rex-form-col-a rex-form-checkbox rex-form-label-right">
			<input class="rex-form-checkbox" type="checkbox" id="subcats" name="subcats" value="1" <?php if ($subcats == "1") echo "checked"; ?> />
			<label for="subcats"><?php echo $rxa_shadowbox['i18n']->msg('text_settings_subcats'); ?></label>
		</p>
	</div>

	<div class="rex-form-row">
        <p class="rex-form-col-a rex-form-select">
			<label for="id_category_select"><?php echo $rxa_shadowbox['i18n']->msg('text_settings_help'); ?></label>
<?php
	echo $select_cats;
?>
		</p>
	</div>

	<div class="rex-form-row">
		<p class="rex-form-col-a rex-form-text">
			<label for="excludeids"><?php echo $rxa_shadowbox['i18n']->msg('text_settings_excludeids'); ?></label>
			<input class="rex-form-text" type="text" id="excludeids" name="excludeids" value="<?php echo $rxa_shadowbox['excludeids']; ?>" />
			<span class="rex-form-notice"><?php echo $rxa_shadowbox['i18n']->msg('text_settings_excludeids2'); ?></span>
		</p>
	</div>

	<div class="rex-form-row">
		<p class="rex-form-col-a rex-form-submit">
			<br /><input type="submit" class="rex-form-submit" name="sendit" value="<?php echo $rxa_shadowbox['i18n']->msg('button_save'); ?>" /><br /><br />
		</p>
	</div>	

</div>
</fieldset>
</form>

</div>
</div>

<?php echo $rxa_compat['backendsuffix']; ?>
