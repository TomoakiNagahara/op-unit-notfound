<?php
/**
 * unit-notfound:/Common.class.php
 *
 * @creation  2019-02-06
 * @version   1.0
 * @package   unit-notfound
 * @author    Tomoaki Nagahara <tomoaki.nagahara@gmail.com>
 * @copyright Tomoaki Nagahara All right reserved.
 */

/** namespace
 *
 * @creation  2019-02-06
 */
namespace OP\UNIT\NOTFOUND;

/** Common
 *
 * @creation  2019-02-06
 * @version   1.0
 * @package   unit-notfound
 * @author    Tomoaki Nagahara <tomoaki.nagahara@gmail.com>
 * @copyright Tomoaki Nagahara All right reserved.
 */
class Common
{
	/** trait.
	 *
	 */
	use \OP_CORE;

	/** Get configuration.
	 *
	 * @return	 array		 $config
	 */
	static private function _Config()
	{
		//	Get config from Env.
		if(!$config = \Env::Get('notfound') ){
		//	$this->Unit('notfound')->Help('config');
			return;
		};

		//	If given DSN.
		if( $dsn = $config['dsn'] ?? null ){
			//	Parse DSN.
			$config = array_merge(self::DSN($dsn), $config);
			$config['dsn'] = null;

			//	Save parse result.
			\Env::Set('notfound', $config);
		};

		//	...
		return $config;
	}

	/** Get IF_DATABASE object.
	 *
	 * @return \IF_DATABASE
	 */
	static function DB()
	{
		//	...
		static $_DB;

		//	...
		if(!$_DB ){
			$_DB = \Unit::Instantiate('Database');
			$_DB->Connect( self::_Config() );
		};

		//	...
		return $_DB->isConnect() ? $_DB: false;
	}

	/** Generate common hash.
	 *
	 * @param	 string		 $str
	 * @return	 string		 $hash
	 */
	static function Hash(string $str):string
	{
		/** CAUTION
		 *
		 *  Salt is commonlize.
		 *  Because that for sharing with all applications.
		 *
		 */
		return Hasha1($str, 10, '');
	}
}
