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
 * Workaround für rex_title
 */
if (!function_exists('rex_title'))
{
	function rex_title($msg, $subpages)
	{
		title($msg, $subpages);
	}
} // End function_exists

/**
 * rex_info für REDAXO < 4.1.0
 */
if (!function_exists('rex_info')) {
	function rex_info($message, $cssClass = null, $sorround_tag = null)
	{
	  return '<p class="rex-message rex-info"><span>'. $message .'</span></p>';
	}
} // End function_exists

/**
 * rex_put_file_contents für REDAXO < 4.1.0
 */
if (!function_exists('rex_put_file_contents')) {
	function rex_put_file_contents($path, $content)
	{
	  global $REX;

	  $writtenBytes = file_put_contents($path, $content);
	  @ chmod($path, $REX['FILEPERM']);

	  return $writtenBytes;
	}
} // End function_exists

/**
 * rex_get_file_contents für REDAXO < 4.1.0
 */
if (!function_exists('rex_get_file_contents')) {
	function rex_get_file_contents($path)
	{
	  return file_get_contents($path);
	}
} // End function_exists

/**
 * rex_replace_dynamic_contents für REDAXO < 4.1.0
 */
if (!function_exists('rex_replace_dynamic_contents')) {
	function rex_replace_dynamic_contents($path, $content)
	{
	  if($fcontent = rex_get_file_contents($path))
	  {
	    $content = "// --- DYN\n". trim($content) ."\n// --- /DYN";
	    $fcontent = ereg_replace("(\/\/.---.DYN.*\/\/.---.\/DYN)", $content, $fcontent);
	    return rex_put_file_contents($path, $fcontent);
	  }
	  return false;
	}
} // End function_exists

if (!function_exists('rex_request')) {
	function rex_request($varname, $vartype = '', $default = '')
	{
	  return _rex_array_key_cast($_REQUEST, $varname, $vartype, $default);
	}
} // End function_exists

if (!function_exists('_rex_array_key_cast')) {
	function _rex_array_key_cast($haystack, $needle, $vartype, $default = '')
	{
	  if(!is_array($haystack))
	  {
	    trigger_error('Array expected for $haystack in _rex_array_key_cast()!', E_USER_ERROR);
	    exit();
	  }

	  if(!is_scalar($needle))
	  {
	    trigger_error('Scalar expected for $needle in _rex_array_key_cast()!', E_USER_ERROR);
	    exit();
	  }

	  if(array_key_exists($needle, $haystack))
	  {
	    $var = $haystack[$needle];
	    return _rex_cast_var($var, $vartype);
	  }

	  return _rex_cast_var($default, $vartype);
	}
} // End function_exists

if (!function_exists('_rex_cast_var')) {
	function _rex_cast_var($var, $vartype)
	{
	  if(!is_string($vartype))
	  {
	    trigger_error('String expected for $vartype in _rex_cast_var()!', E_USER_ERROR);
	    exit();
	  }

	  // Variable Casten
	  switch($vartype)
	  {
	    case 'bool'   :
	    case 'boolean': $var = (boolean) $var; break;
	    case 'int'    :
	    case 'integer': $var = (int)     $var; break;
	    case 'double' : $var = (double)  $var; break;
	    case 'float'  : $var = (float)   $var; break;
	    case 'string' : $var = (string)  $var; break;
	    case 'object' : $var = (object)  $var; break;
	    case 'array'  : $var = (array)   $var; break;

	    // kein Cast, nichts tun
	    case ''       : break;

	    // Evtl Typo im vartype, deshalb hier fehlermeldung!
	    default: trigger_error('Unexpected vartype "'. $vartype .'" in _rex_cast_var()!', E_USER_ERROR); exit();
	  }

	  return $var;
	}
} // End function_exists

	// Fix für REDAXO 3.1
	if (isset($REX['version']) and isset($REX['subversion']) and ($REX['version']=='3.1') and ($REX['subversion']=='1'))
	{
		if (isset($REX['STARTARTIKEL_ID']))
		{
			$REX['START_ARTICLE_ID'] = $REX['STARTARTIKEL_ID'];
		}
		if (!isset($REX['CUR_CLANG']))
		{
			$REX['CUR_CLANG'] = rex_request('clang', 'string');
		}
		if (!isset($REX['ARTICLE_ID']) and isset($article_id))
		{
			$REX['ARTICLE_ID'] = $article_id;
		}
	}
	
	if (!isset($REX['DIRPERM']))
	{
		$REX['DIRPERM'] = octdec(775);
	}

	
	// Fix für Backend Output	
	$rxa_compag = array();
	$rxa_compat['backendprefix']='';
	$rxa_compat['backendsuffix']='';
	if (isset($REX['version']) and isset($REX['subversion']) and ($REX['version']=='3.1') and ($REX['subversion']=='1'))
	{
		$rxa_compat['backendprefix'] = '<table style="width: 770px" cellpadding="10" cellspacing="0"><tr><td class="grey">';
		$rxa_compat['backendsuffix'] = '</td></tr></table>';
		$REX['RE_ID'][0]=array();
	}
	if (isset($REX['VERSION']) and ($REX['VERSION']=='3'))
	{
		$rxa_compat['backendprefix'] = '<table style="width: 770px" cellpadding="10" cellspacing="0"><tr><td class="grey">';
		$rxa_compat['backendsuffix'] = '</td></tr></table>';
		$REX['RE_ID'][0]=array();
	}
