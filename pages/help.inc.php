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
<h2 class="rex-hl2"><?php echo $rxa_shadowbox['i18n']->msg('menu_information'); ?></h2>
<div class="rex-addon-content">

<?php
	include_once ($rxa_shadowbox['path'].'/help.inc.php');
?>

</div>
</div>

<?php echo $rxa_compat['backendsuffix']; ?>