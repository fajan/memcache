<?php
/**
 * DokuWiki Plugin memcache (Helper Component)
 *
 * @license GPL 2 http://www.gnu.org/licenses/gpl-2.0.html
 * @author  János Fekete <jan@fjan.eu>
 */

// must be run within Dokuwiki
if(!defined('DOKU_INC')) die();

class helper_plugin_memcache extends DokuWiki_Plugin {

	public function __construct() {
		global $INPUT;
		parent::__construct();
		if (!defined())
		$this->loadConfig(); 
		/* 	
			NOTE: as both apc and wincache supports arrays for many functions, this class DOES NOT.
			The variables are not checked for better performance, but checking can be enabled for debuging in which case non-string keys trigger error.
		*/
		if (!defined('MEMCACHE_CHECK_KEYS')) define('MEMCACHE_CHECK_KEYS',(bool)$this->conf['debug_check_keys']);
		require_once('classes/memcache_interface.loader.php');

	}
    /**
     * Return info about supported methods in this Helper Plugin
     *
     * @return array of public methods
     */
    function getMethods() {
        $result = array();
        $result[] = array(
                'name'   => 'key_checks_enabled',
                'desc'   => "returns MEMCACHE_CHECK_KEYS constant's value.",
				'return' => array('enabled'=>'boolean'),
                );
        $result[] = array(
                'name'   => 'driver',
                'desc'   => 'returns the backend driver as string (which is either "wincache" or "apc" currently or "fake" if neither is supported, and using filesys-caching instead.)',
				'return' => array('driver'=>'string'),
                );
        $result[] = array(
                'name'   => 'emulated',
                'desc'   => 'returns true if the driver is emulated (no performance boost)',
				'return' => array('emulated'=>'boolean'),
                );
        $result[] = array(
                'name'   => 'add',
                'desc'   => "add one entry, if it does not exists already. returns boolean success (false if key already exists or something went wrong)\n if ttl parameter is given and positive, it will set time-to-live, else it will remain until cache clear.",
				'parameters' => array(
					'key' => 'string',
					'val' => 'mixed (serializable)',
					'ttl' => 'integer',
					),
				'return' => array('success'=>'boolean'),
                );
        $result[] = array(
                'name'   => 'set',
                'desc'   => "set one entry: create new or overwrite old value. returns boolean success (false if something went wrong). if ttl parameter is given and positive, it will set time-to-live, else it will remain until cache clear.",
				'parameters' => array(
					'key' => 'string',
					'val' => 'mixed (serializable)',
					'ttl' => 'integer',
					),
				'return' => array('success'=>'boolean'),
                );
        $result[] = array(
                'name'   => 'exists',
                'desc'   => "checks if a key exists.",
				'parameters' => array(
					'key' => 'string',
					),
				'return' => array('exists'=>'boolean'),
                );
        $result[] = array(
                'name'   => 'del',
                'desc'   => "deletes data by its key.",
				'parameters' => array(
					'key' => 'string',
					),
				'return' => array('success'=>'boolean'),
                );
        $result[] = array(
                'name'   => 'get',
                'desc'   => "retrieves a value. returns retrieved value or null. $success out-parameter can be checked to check success (you may have false, null, 0, or "" as stored value).",
				'parameters' => array(
					'key' => 'string',
					'success' => 'boolean (out)',
					),
				'return' => array('value'=>'mixed'),
                );
       $result[] = array(
                'name'   => 'clear',
                'desc'   => "clears the entire cache. (note: server-wide, so clears cache for every code that uses the same driver).  returns boolean success (which should be always true)",
				'return' => array('success'=>'boolean'),
                );

        return $result;
    }

	function key_checks_enabled(){
		return (bool) @MEMCACHE_CHECK_KEYS;
	}

	function driver(){
		return memcache::driver();
	}

	function emulated(){
		return memcache::emulated();
	}
	
	function clear(){
		return memcache::clear();
	}
	
	function add($key,$val,$ttl = 0){
		return memcache::add($key,$val,$ttl);
	}

	function set($key,$val,$ttl = 0){
		return memcache::set($key,$val,$ttl);
	}
	
	function exists($key){
		return memcache::exists($key);
	}
	
	function del($key){
		return memcache::del($key);
	}	
	
	function get($key,&$success = false){
		return memcache::get($key,$success);
	}	
	
	 
}

// vim:ts=4:sw=4:et:
