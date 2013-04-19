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
class syntax_plugin_alphalist extends DokuWiki_Syntax_Plugin {

    function getPType(){
       return 'block';
    }

    function getType() { return 'substition'; }
    function getSort() { return 32; }


    function connectTo($mode) {
	$this->Lexer->addSpecialPattern('\[alphalist .*?\]',$mode,'plugin_filterrss');
    }

    function handle($match, $state, $pos, &$handler) {

	$known_fileds = array('pubDate', 'title', 'description', 'link');
	$opposite_signs = array('>' => '<', '<' => '>', '>=' => '<=', '<=' => '>=');

	$exploded = explode(' ', $match);
	$url = $exploded[1];

	//we have no arguments
	if(count($exploded) < 3)
	{
	    //Remove ] from the end
	    $url = substr($url, 0, -1);
	    return array('url' => $url, 'conditions' => array());
	}
	array_shift($exploded);
	array_shift($exploded);


	$conditions = implode('', $exploded);
	
	//Remove ] from the end
	$conditions = substr($conditions, 0, -1);

	$cond_array = explode('&&', $conditions);

	$cond_output = array();

	foreach($cond_array as $cond)
	{
	    preg_match('/(.*?)(>|<|=|>=|<=)+(.*)/', $cond, $res);
	    if(in_array($res[1], $known_fileds))
	    {
		$name = $res[1];
		$value = $res[3];
		$sign = $res[2];
	    } elseif(in_array($res[3], $known_fileds))
	    {
		$name = $res[3];
		$value = $res[1];
		$sign = $opposite_signs[$res[2]];
	    } else
	    {
		continue;
	    }

	    //remove "" and ''
	    $value = str_replace(array('"', "'"), '', $value);

	    if(!isset($cond_output[$name]))
		$cond_output[$name] = array();

		array_push($cond_output[$name], array($sign, $value));
	}
	return array('url' => $url, 'conditions' => $cond_output);
    }

    function render($mode, &$renderer, $data) {
        if($mode == 'xhtml') {

	    $alphalist =& plugin_load('helper', 'alphalist');
	    $renderer->doc .= 'Mongo';
	    return true;
        }
        return false;
    }
}
