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
	$this->Lexer->addSpecialPattern('\[alphalist .*?\]',$mode,'plugin_alphalist');
    }

    function handle($match, $state, $pos, &$handler)
    {
	//remove ]
	$match = substr($match, 0, -1);
	$pages = explode(' ', $match);
	//remove [alphalist
	array_shift($pages);
	$list = array();
	foreach($pages as $v)
	{
	    $file = wikiFN($v);
	    if(file_exists($file))
	    {
		$content = file($file);
		foreach($content as $row)
		{
		    if(preg_match('/^  (\-|\*)(.*)/', $row, $match))
		    {
			$list[] = $match[2];
		    }
		}
	    }
	}
	return $list;
    }

    function render($mode, &$renderer, $data) {
        if($mode == 'xhtml') {

	    $alphalist =& plugin_load('helper', 'alphalist');

	    if(count($data) > 0)
	    {
		sort($data);

		$list_cont = '';

		$first_letter = '';
		$letter_change = false;

		$req_first_letter = '/^.*?([a-zA-ZąćęłńóśźżĄĆĘŁŃÓŚŹŻ]).*$/u'; 
		foreach($data as $v)
		{
		    //if dokuwiki link - get value
		    if(strstr($v, '|'))
		    {
			$ex = explode('|', $v);
			preg_match($req_first_letter, $ex[1], $letter);
			$f_letter = $letter[1];
		    } else
		    {
			//Get first letter that isn't wiki syntax
			preg_match($req_first_letter, $v, $letter);
			$f_letter = $letter[1];
		    }
		    if($f_letter != $first_letter)
		    {
			$letter_change = true;
			$first_letter = $f_letter;
		    }
		    if($letter_change == true)
		    {
			$list_cont .= '==='.$first_letter."===\n";
			$letter_change = false;
		    }
		    $list_cont .= '  - '.$v."\n";
		}
		$renderer->doc .= $alphalist->parse($list_cont);
	    }

	    return true;
        }
        return false;
    }
}
