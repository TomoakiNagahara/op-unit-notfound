<?php
/**
 * unit-notfound:/NotFound.class.php
 *
 * @creation  2019-01-29
 * @version   1.0
 * @package   unit-notfound
 * @author    Tomoaki Nagahara <tomoaki.nagahara@gmail.com>
 * @copyright Tomoaki Nagahara All right reserved.
 */

/** namespace
 *
 * @creation  2019-01-29
 */
namespace OP\UNIT;

/** Used class
 *
 */
use OP\UNIT\NOTFOUND\Common;

/** NotFound
 *
 * @creation  2019-01-29
 * @version   1.0
 * @package   unit-notfound
 * @author    Tomoaki Nagahara <tomoaki.nagahara@gmail.com>
 * @copyright Tomoaki Nagahara All right reserved.
 */
class NotFound implements \OP\IF_UNIT
{
	/** trait.
	 *
	 */
	use \OP\OP_CORE, \OP\OP_UNIT;

	/** Will execute automatically.
	 *
	 */
	function Auto()
	{
		//	...
		if( $DB = $this->_DB() ){
			$host = self::_Host( $DB );
			$uri  = self::_URI(  $DB );
			$ua   = self::_UA(   $DB );
					self::_NotFound( $DB, $host, $uri, $ua );
		};
	}

	/** Get IF_DATABASE object.
	 *
	 * @return \IF_DATABASE
	 */
	private function _DB()
	{
		/* @var $_DB \IF_DATABASE */
		static $_DB;

		//	...
		if( $_DB === null ){
			$_DB = $this->Unit('Database');

			//	...
			if( get_class($_DB) === 'OP\Ghost' ){
				$_DB = false;
			}else{
				$_DB->Connect( Common::_Config() );
			};
		};

		//	...
		return $_DB;
	}

	/** Host name
	 *
	 * @param	\IF_DATABASE $DB
	 * @return	 int		 $ai
	 */
	private function _Host( \IF_DATABASE $DB ):int
	{
		//	...
		$table = 't_host';
		$host  = $_SERVER['SERVER_NAME'];
	//	$port  = $_SERVER['SERVER_PORT'];
		$hash  = NOTFOUND\Common::Hash($host);

		//	...
		if( $ai = $DB->Quick(" ai <- {$table}.hash = {$hash} ", ['limit'=>1]) ){
			//	Exists
		}else{
			//	...
			$config = [];
			$config['table'] = $table;
			$config['set']['hash'] = $hash;
			$config['set']['host'] = $host;

			//	...
			$ai = $DB->Insert($config);
		};

		//	...
		return $ai;
	}

	/** URI
	 *
	 * @param	\IF_DATABASE $DB
	 * @return	 int		 $ai
	 */
	private function _URI( \IF_DATABASE $DB ):int
	{
		//	...
		$uri   = $_SERVER['REQUEST_URI'];

		//	...
		if( $pos  = strpos($uri, '?') ){
			$path = substr($uri, 0, $pos);
		}else{
			$path = $uri;
		}

		//	...
		$table = 't_uri';
		$hash  = NOTFOUND\Common::Hash($path);

		//	...
		if( $ai = $DB->Quick(" ai <- {$table}.hash = {$hash} ", ['limit'=>1]) ){
			//	Exists
		}else{
			//	...
			$config = [];
			$config['table'] = $table;
			$config['set']['hash'] = $hash;
			$config['set']['uri']  = $path;

			//	...
			$ai = $DB->Insert($config);
		};

		//	...
		return $ai;
	}

	/** User agent
	 *
	 * @param	\IF_DATABASE $DB
	 * @return	 int		 $ai
	 */
	private function _UA( \IF_DATABASE $DB ):int
	{
		//	...
		$ua = $_SERVER['HTTP_USER_AGENT'] ?? '';

		//	...
		$table = 't_ua';
		$hash  = NOTFOUND\Common::Hash($ua);

		//	Get ai, Insert if does not exist.
		if(!$ai = $DB->Quick(" ai <- {$table}.hash = {$hash} ", ['limit'=>1]) ){
			//	...
			$config = [];
			$config['table'] = $table;
			$config['set']['hash']    = $hash;
			$config['set']['ua']      = $ua;

			//	...
			$ai = $DB->Insert($config);
		};

		//	Get t_ua record.
		$record = $DB->Quick(" {$table}.ai = {$ai} ", ['limit'=>1]);

		//	If has not been set, it set.
		if(!$record['os'] or !$record['browser'] ){
			//	...
			$config = [];
			$config['table'] = $table;
			$config['limit'] = 1;
			$config['where'][] = " ai = $ai ";

			//	...
			if(!$record['os'] ){
				$config['set']['os']      = self::_OS($ai, $ua);
			};

			//	...
			if(!$record['browser'] ){
				$config['set']['browser'] = self::_Browser($ai, $ua);
			};

			//	...
			$DB->Update($config);
		};

		//	...
		return $ai;
	}

	/** OS
	 *
	 * @param	 integer	 $ua_ai
	 * @param	 string		 $ua
	 * @return	 int|null	 $ai
	 */
	private function _OS( $ua_ai, $ua )
	{
		//	...
		$table = 't_ua_os';

		//	Search OS name and OS version.
		$m = [];
		foreach( include(__DIR__.'/config/os.php') as $os => $preg ){
			//	...
			if( preg_match("/$preg/", $ua, $m) ){
				//	...
				$version = $m[1].'.'.$m[2];
				break;
			};
		};

		//	If they do not match, it returns.
		if( empty($version) ){
			return null;
		};

		//	Select config.
		$config = [];
		$config['table'] = $table;
		$config['limit'] = 1;
		$config['where'][] = "os = $os";
		$config['where'][] = "version = $version";

		//	Insert if does not exist.
		if( NOTFOUND\Common::DB()->Count($config) === 0 ){
			$config = [];
			$config['table'] = $table;
			$config['set'][] = "ua = $ua_ai";
			$config['set'][] = "os = $os";
			$config['set'][] = "version = $version";
			$ai = NOTFOUND\Common::DB()->Insert($config);
		};

		//	...
		return $ai ?? null;
	}

	/** Browser
	 *
	 * @param	 integer	 $ua_ai
	 * @param	 string		 $ua
	 * @return	 int|null	 $ai
	 */
	private function _Browser( $ua_ai, $ua )
	{
		//	...
		$table = 't_ua_browser';

		//	...
		$m = [];
		foreach( include(__DIR__.'/config/browser.php') as $browser => $preg ){
			//	...
			if( preg_match("/$preg/", $ua, $m) ){
				//	...
				$version = $m[1].'.'.$m[2];
				break;
			};
		};

		//	If they do not match, it returns.
		if( empty($version) ){
			return null;
		};

		//	...
		$config = [];
		$config['table'] = $table;
		$config['limit'] = 1;
		$config['where'][] = "browser = $browser";
		$config['where'][] = "version = $version";

		//	Insert if does not exist.
		if( NOTFOUND\Common::DB()->Count($config) === 0 ){
			$config = [];
			$config['table'] = $table;
			$config['set'][] = "ua = $ua_ai";
			$config['set'][] = "browser = $browser";
			$config['set'][] = "version = $version";
			$ai = NOTFOUND\Common::DB()->Insert($config);
		}

		//	...
		return $ai ?? null;
	}

	/** NotFound
	 *
	 * @param	\IF_DATABASE $DB
	 * @param	 string		 $host
	 * @param	 string		 $uri
	 * @param	 string		 $ua
	 * @return	 int		 $count
	 */
	private function _NotFound( \IF_DATABASE $DB, int $host, int $uri, int $ua ):int
	{
		//	...
		$table = 't_notfound';

		//	...
		$config = [];
		$config['table'] = $table;
		$config['where'][] = "host = $host";
		$config['where'][] = "uri  = $uri";
		$config['where'][] = "ua   = $ua";
		$config['limit'] = 1;

		//	...
		$count = ( $record = $DB->Select($config) ) ? $record['count']: 0;
		$count++;

		//	...
		if( $count === 1 ){
			//	insert
			$config['set'] = $config['where'];
			$config['set'][] = "count = $count";
			unset($config['where']);
			unset($config['limit']);

			//	...
			$DB->Insert($config);
		}else{
			//	update
			$config['set'][] = "count = $count";

			//	...
			$DB->Update($config);
		};

		//	...
		return $count;
	}

	/** Will execute automatically of Admin.
	 *
	 */
	function Admin()
	{
		include_once(__DIR__.'/admin/Admin.class.php');
		NOTFOUND\Admin::Auto();
	}
}
