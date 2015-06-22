<?php

class memcache_wincache implements memcache_interface{
	public static function init() {}

	public static function driver(){ return "wincache";}

	public static function emulated() { return false; }
	
	public static function add($key, $val,$ttl = 0){ 
		if (MEMCACHE_CHECK_KEYS && !is_string($key)) trigger_error("The key needs to be string! (note: not even numbers are accepted)",E_USER_ERROR);
		return wincache_ucache_add($key,$val,$ttl);
	}
	
	public static function set($key, $val,$ttl = 0){ 
		if (MEMCACHE_CHECK_KEYS && !is_string($key)) trigger_error("The key needs to be string! (note: not even numbers are accepted)",E_USER_ERROR);
		return wincache_ucache_set($key,$val,$ttl);
	}
	
	public static function exists($key){ 
		if (MEMCACHE_CHECK_KEYS && !is_string($key)) trigger_error("The key needs to be string! (note: not even numbers are accepted)",E_USER_ERROR);
		return wincache_ucache_exists ($key);
	}
	
	public static function del($key){
		if (MEMCACHE_CHECK_KEYS && !is_string($key)) trigger_error("The key needs to be string! (note: not even numbers are accepted)",E_USER_ERROR);
		return wincache_ucache_delete ($key);
	}
	public static function get($key,&$success = false){
		if (MEMCACHE_CHECK_KEYS && !is_string($key)) trigger_error("The key needs to be string! (note: not even numbers are accepted)",E_USER_ERROR);
		return wincache_ucache_get  ($key, $success);
	}
	
	public static function clear(){
		return wincache_ucache_clear();
	}
	
}

memcache_wincache::init();