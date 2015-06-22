<?php
class memcache_fakecache implements memcache_interface{
//	--- helper methods / properties ----
	protected static $cache_dir, $ttl_dir;
	/* prepares a key name to be valid filename */
	protected static function normalize_key($key){
		return preg_replace_callback('/(\W)/',
			function($m){ return "-".ord($m[1])."-"; },
			$key);
	}
	/* undos the filename normalization */
	protected static function denormalize_key($key){
		return preg_replace_callback('/-(\d+)-/',
			function($m){ return chr($m[1]); },
			$key);
	}
	public static function gc(){	// deletes all entries which are over of their TTL.
		$dh = opendir(static::$ttl_dir);
		while ($file = readdir($dh)){
			if ($file == "." || $file == "..") continue;
			if (file_get_contents(static::$ttl_dir.$file) < time()){
				@unlink(static::$ttl_dir.$file);
				@unlink(static::$cache_dir.$file);
			}
		}
		closedir($dh);
	}
//	---- standard methods ----
	public static function init(){
		global $conf;
		static::$cache_dir = $conf['tmpdir'].'/cachewrapper/'; 
		static::$ttl_dir = $conf['tmpdir'].'/cachewrapper_ttl/'; 
		if (!file_exists(static::$cache_dir)) mkdir(static::$cache_dir);
		elseif (!is_dir(static::$cache_dir)) trigger_error("The cache directory '".static::$cache_dir."' is not a directory!",E_USER_ERROR);
		if (!file_exists(static::$ttl_dir)) mkdir(static::$ttl_dir);
		elseif (!is_dir(static::$ttl_dir)) trigger_error("The cache directory '".static::$cache_dir."' is not a directory!",E_USER_ERROR);
		static::gc();
		//register_shutdown_function(array(__CLASS__,'gc'));	// registering shutdown function to do garbage collections.
	}

	public static function emulated() { return true; }

	public static function driver(){ return "fake";}
	
	public static function add($key, $val,$ttl = 0){ 
		if (MEMCACHE_CHECK_KEYS && !is_string($key)) trigger_error("The key needs to be string! (note: not even numbers are accepted)",E_USER_ERROR);
		if ($result = (!file_exists(static::$cache_dir.static::normalize_key($key)) && @file_put_contents(static::$cache_dir.static::normalize_key($key),gzcompress(serialize($val),3)) !== false)){
			if ($ttl > 0) 			file_put_contents(static::$ttl_dir.static::normalize_key($key),time()+$ttl);
			else 					@unlink(static::$ttl_dir.static::normalize_key($key));
		}
		return (bool)$result;
	}
	
	public static function set($key, $val,$ttl = 0){ 
		if (MEMCACHE_CHECK_KEYS && !is_string($key)) trigger_error("The key needs to be string! (note: not even numbers are accepted)",E_USER_ERROR);
		if ($result = (@file_put_contents(static::$cache_dir.static::normalize_key($key),gzcompress(serialize($val),3)) !== false)){
			if ($ttl > 0)			file_put_contents(static::$ttl_dir.static::normalize_key($key),time()+$ttl);
			else 					@unlink(static::$ttl_dir.static::normalize_key($key));
		}
		return (bool)$result;
	}
	
	public static function exists($key){ 
		if (MEMCACHE_CHECK_KEYS && !is_string($key)) trigger_error("The key needs to be string! (note: not even numbers are accepted)",E_USER_ERROR);
		return file_exists(static::$cache_dir.static::normalize_key($key)); 
	}

	public static function del($key){
		if (MEMCACHE_CHECK_KEYS && !is_string($key)) trigger_error("The key needs to be string! (note: not even numbers are accepted)",E_USER_ERROR);
		@unlink(static::$ttl_dir.static::normalize_key($key));
		return @unlink(static::$cache_dir.static::normalize_key($key));
	}
	
	public static function get($key,&$success = false){
		if (MEMCACHE_CHECK_KEYS && !is_string($key)) trigger_error("The key needs to be string! (note: not even numbers are accepted)",E_USER_ERROR);
		$val = null;
		$success = (																						// success is based on evaluation order to detect unsuccessful event early and prevent later instructions 
				file_exists(static::$cache_dir.static::normalize_key($key)) 								// file exists? (if not, abort with fail, as in every other step bellow)
				&& ($cnt = file_get_contents(static::$cache_dir.static::normalize_key($key))) !== false		// file can be read?
				&& ($ucnt = @gzuncompress($cnt)) !== false													// file is uncompressable
				&& (																						// the resulted var is:
					($val = @unserialize($ucnt)) !== false 													// not boolean false
					|| $ucnt == serialize(false)															// is boolean false, but it should be.
					)					
			); 
		return $val;
	}

	public static function clear(){
		$dh = opendir(static::$cache_dir);
		while ($file = readdir($dh)){
			if ($file == "." || $file == "..") continue;
			@unlink(static::$cache_dir.$file);
		}
		closedir($dh);
		$dh = opendir(static::$ttl_dir);
		while ($file = readdir($dh)){
			if ($file == "." || $file == "..") continue;
			@unlink(static::$ttl_dir.$file);
		}
		closedir($dh);
		return true;
	}

	
}

memcache_fakecache::init();