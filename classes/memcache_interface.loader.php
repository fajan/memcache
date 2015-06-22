<?php

   
interface memcache_interface{
	/* initialization of the driver. If the driver needs destructor function, it should register it for self. */
	public static function init();				

	/* the driver is emulated (no performance boost). */
	public static function emulated();

	/* returns the backend driver as string (which is either "wincache" or "apc" currently or "fake" if neither is supported, and using filesys-caching instead.) */
	public static function driver();

	/* add one entry, if it does not exists already. returns boolean success (false if key already exists or something went wrong)
		if ttl parameter is given and positive, it will set time-to-live, else it will remain until cache clear. */
	public static function add($key,$val,$ttl = 0);		

	/* set one entry: create new or overwrite old value. returns boolean success (false if something went wrong)
		if ttl parameter is given and positive, it will set time-to-live, else it will remain until cache clear. */
	public static function set($key,$val,$ttl = 0);		
	
	/* checks if a key exists. returns boolean. */
	public static function exists($key);
	
	/* deletes data by key. returns boolean. */
	public static function del($key);
	
	/* retrieves a value. returns retrieved value or null. $success out-parameter can be checked to check success (you may have false, null, 0, or "" as stored value). */
	public static function get($key,&$success = false);

	/* clears the entire cache. (note: server-wide, so clears cache for every code that uses the same driver).  returns boolean success (which should be always true) */
	public static function clear();
	
}
if (!class_exists('memcache')){
	if (extension_loaded('apc') || extension_loaded('apcu')){
		require_once('memcache_apc.class.php');
		class_alias('memcache_apc','memcache');
	}
	elseif(extension_loaded('wincache')){
		require_once('memcache_wincache.class.php');
		class_alias('memcache_wincache','memcache');
	}
	else{
		require_once('memcache_fakecache.class.php');
		class_alias('memcache_fakecache','memcache');
	}
}