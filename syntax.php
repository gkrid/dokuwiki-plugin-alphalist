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

	$alphalist =& plugin_load('helper', 'alphalist');

	//remove ]
	$match = substr($match, 0, -1);
	$explode = explode(' ', $match);
	//remove [alphalist
	array_shift($explode);

	//join if splitet between {
	
	$pages = array();
	$j = 0;
	for($i=0;$i<count($explode);$i++,$j++)
	{
	    $pages[$j] = $explode[$i]; 
	    //there is { in string
	    if(strstr($explode[$i], '{') != false)
	    {
		//start imploding
		do
		{
		    $i++;
		    $pages[$j] .= ' '.$explode[$i];
		} while(strstr($explode[$i], '}') == false);
	    }
	}

	$list = array();
	foreach($pages as $v)
	{
	    $section = false;
	    //Get section
	    if(preg_match('/^(.*?)\{(.*?)\}$/', $v, $data))
	    {
		$v = $data[1];
		$section = $data[2];
	    }

	    $file = wikiFN($v);
	    if(file_exists($file))
	    {
		$content = file($file);
		if($section == false)
		{
		    foreach($content as $row)
		    {
			if(preg_match('/^  (\-|\*)(.*)/', $row, $match))
			{
			    $list[$alphalist->plain($match[2])] = $match[2];
			}
		    }
		} else
		{
		    //0 - waiting for header 1 - in header 
		    $state = 0;
		    foreach($content as $row)
		    {
			if($state == 0)
			{
			    if(strstr($row, $section))
				$state++;
			} else
			{
			    if(preg_match('/======.*?======/', $row))
				break;

			    if(preg_match('/^  (\-|\*)(.*)/', $row, $match))
			    {
				$list[$alphalist->plain($match[2])] = $match[2];
			    }
			}
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
		ksort($data, SORT_LOCALE_STRING);

		$list_cont = '';

		$first_letter = '';
		$letter_change = false;

		foreach($data as $k => $v)
		{
		    $f_letter = mb_substr($k, 0, 1);
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
