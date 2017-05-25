<?php
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
    }
    function parse($string)
    {
	$info = array();
	$rendered = p_render('xhtml',p_get_instructions($string),$info);

	dbglog($string, 'alphalist helper::parse before');
	dbglog($rendered, 'alphalist helper::parse after');

	return $rendered;
	
    }
    function plain($string)
    {
	$doku_inline_tags = array('**', '//', "''", '<del>', '</del>', ']]');
	$plain = str_replace($doku_inline_tags, '', $string);
	$req_link = '/\[\[(.*?\|)?/';
	$plain = preg_replace($req_link, '', $plain);

	dbglog($string, 'alphalist helper::plain before');
	dbglog(trim($plain), 'alphalist helper::plain after');

	return trim($plain);
    }
}

