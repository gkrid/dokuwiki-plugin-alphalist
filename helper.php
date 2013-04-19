<?php
/**
 * Plugin Now: Inserts a timestamp.
 * 
 * @license    GPL 3 (http://www.gnu.org/licenses/gpl.html)
 * @author     Szymon Olewniczak <szymon.olewniczak@rid.pl>
 */

// must be run within DokuWiki
if(!defined('DOKU_INC')) die();

if(!defined('DOKU_PLUGIN')) define('DOKU_PLUGIN',DOKU_INC.'lib/plugins/');
require_once DOKU_PLUGIN.'syntax.php';

/**
 * All DokuWiki plugins to extend the parser/rendering mechanism
 * need to inherit from this class
 */
class helper_plugin_alphalist extends dokuwiki_plugin
{
    function getMethods(){
      $result = array();
      $result[] = array(
	'name'   => 'parse',
	'desc'   => 'change dokuwiki syntax to html',
	'params' => array('string' => 'string'),
	'return' => array('content' => 'string'),
      );
      $result[] = array(
	'name'   => 'plain',
	'desc'   => 'convert dokuwiki syntax to plain text',
	'params' => array('string' => 'string'),
	'return' => array('plain' => 'string'),
      );
      $result[] = array(
	'name'   => 'ksort_hum',
	'desc'   => 'key sort with polish charters',
	'params' => array('string' => 'string'),
	'return' => array('plain' => 'string'),
      );
    }
    function parse($string)
    {
	$info = array();
	return p_render('xhtml',p_get_instructions($string),$info);
    }
    function plain($string)
    {
	$doku_inline_tags = array('**', '//', "''", '<del>', '</del>', ']]');
	$plain = str_replace($doku_inline_tags, '', $string);
	$req_link = '/\[\[(.*?\|)?/';
	$plain = preg_replace($req_link, '', $plain);
	return trim($plain);
    }

    function ksort_hun($array)
    {
	    uksort($array, 'helper_plugin_alphalist::huncmp');
	    return $array;
    }

    function intcmp($a,$b,$ALP)
    {
	    if ($a==$b) return 0;
	    $ALPL = strlen($ALP);

	    $ap = $bp = -1;
	    $i = 0;

	    while (($i < $ALPL) and (($ap == -1) or ($bp == -1)))
	    {
		    if ($ALP[$i] == $a) $ap = $i;
		    if ($ALP[$i] == $b) $bp = $i;
		    $i++;
	    }

	    return($ap < $bp) ? -1 :1;
    }

    function huncmp($astring, $bstring)
    {
	    $ALP = "AaĄąBbCcĆćDdEeĘęFfGgHhIiJjKkLlŁłMmNnOoÓóPpQqRrSsŚśTtUuVvWwXxYyZzŹźŻż";

	    //jeśli równe
	    if ($astring == $bstring) return 0;

	    //wykonuj na kazdym elemencie
	    for ($i = 0; $i < strlen($astring) && $i < strlen($bstring) && $astring[$i] == $bstring[$i]; $i++);

	    //jeśli takie same lecz jedna krótsza
	    if ($astring[$i] == $bstring[$i]) return (strlen($astring) > $bstring) ? -1 : 1;

	    //pierwszy różny znak
	    return(helper_plugin_alphalist::intcmp($astring[$i], $bstring[$i], $ALP));
    }
 
}

