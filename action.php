<?php
// must be run within DokuWiki
if(!defined('DOKU_INC')) die();

if(!defined('DOKU_PLUGIN')) define('DOKU_PLUGIN',DOKU_INC.'lib/plugins/');
require_once DOKU_PLUGIN.'syntax.php';

/**
 * All DokuWiki plugins to extend the parser/rendering mechanism
 * need to inherit from this class
 */
class action_plugin_alphalist extends DokuWiki_Action_Plugin {
    function register(Doku_Event_Handler $controller) {
	$controller->register_hook('PARSER_CACHE_USE', 'BEFORE', $this, '_preventCache', array ());
    }
    /**
     * Prevents page caching
     * @param mixed $param the parameters passed to register_hook when this handler was registered
     * @param object $event event object by reference
     */
    function _preventCache(&$event, $param) 
    {
	$event->preventDefault();
	$event->stopPropagation();
	$event->result = false;
    }
}
