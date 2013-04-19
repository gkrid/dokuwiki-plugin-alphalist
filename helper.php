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
	'name'   => 'bbcode_parse',
	'desc'   => 'parse bbcode to html',
	'params' => array('bbcode_input' => 'string'),
	'return' => array('html_output' => 'string'),
      );
    }
    function bbcode_parse($bbcode_input)
    {
    }
}

