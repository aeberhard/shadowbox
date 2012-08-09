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
?>

<?php echo $rxa_compat['backendprefix']; ?>

<div class="rex-addon-output">
<h2 class="rex-hl2"><?php echo $rxa_shadowbox['i18n']->msg('menu_changelog'); ?></h2>
<div class="rex-addon-content">

<p>
<?php
	if (strstr($REX['LANG'],'utf8'))
	{
		echo utf8_encode(nl2br(htmlspecialchars(file_get_contents($rxa_shadowbox['path'].'/changelog.txt'))));
	}
	else
	{
		echo nl2br(htmlspecialchars(file_get_contents($rxa_shadowbox['path'].'/changelog.txt')));
	}
?>
</p>

</div>
</div>

<?php echo $rxa_compat['backendsuffix']; ?>