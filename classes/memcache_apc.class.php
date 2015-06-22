<?php


class memcache_apc implements memcache_interface{
	public static function init() {}

	public static function driver(){ return "apc";}
	
	public static function emulated() { return false; }

	public static function add($key, $val,$ttl = 0){ 
		if (MEMCACHE_CHECK_KEYS && !is_string($key)) trigger_error("The key needs to be string! (note: not even numbers are accepted)",E_USER_ERROR);
		return apc_add($key,$val,$ttl);
	}

	public static function set($key, $val,$ttl = 0){ 
		if (MEMCACHE_CHECK_KEYS && !is_string($key)) trigger_error("The key needs to be string! (note: not even numbers are accepted)",E_USER_ERROR);
		return apc_store($key,$val,$ttl);
	}

	public static function exists($key){ 
		if (MEMCACHE_CHECK_KEYS && !is_string($key)) trigger_error("The key needs to be string! (note: not even numbers are accepted)",E_USER_ERROR);
		return apc_exists  ($key);
	}

	public static function del($key){
		if (MEMCACHE_CHECK_KEYS && !is_string($key)) trigger_error("The key needs to be string! (note: not even numbers are accepted)",E_USER_ERROR);
		return apc_delete  ($key);
	}
	
	public static function get($key,&$success = false){
		if (MEMCACHE_CHECK_KEYS && !is_string($key)) trigger_error("The key needs to be string! (note: not even numbers are accepted)",E_USER_ERROR);
		return apc_fetch   ($key, $success);
	}

	public static function clear(){
		return apc_clear_cache ("user");
	}

}

memcache_apc::init();