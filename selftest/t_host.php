<?php
/**
 * unit-notfound:/selftest/t_host.php
 *
 * @creation  2019-02-04
 * @version   1.0
 * @package   unit-notfound
 * @author    Tomoaki Nagahara <tomoaki.nagahara@gmail.com>
 * @copyright Tomoaki Nagahara All right reserved.
 */
/* @var $app \OP\UNIT\App */

//  Table configuration.
$app->Unit('selftest')->Config()->Set('table', [
	'name'    => 't_host',
	'charset' => 'utf8',
	'collate' => 'utf8mb4_general_ci',
	'comment' => 'Stack each host name.',
]);

//  Auto incrment id.
$app->Unit('selftest')->Config()->Set('column', [
	'name'    =>  'ai',
	/*
	'type'    => 'int',
	'length'  =>    11,
	'null'    => false,
	'default' =>  null,
	*/
	'ai'      =>  true,
	'comment' => 'Auto increment id.',
]);

//  Hash key.
$app->Unit('selftest')->Config()->Set('column', [
	'name'    => 'hash',
	'type'    => 'char',
	'length'  =>     10,
	'null'    =>  false,
	'collate' => 'ascii_general_ci',
	'comment' => 'Hash by host name.',
	'unique'  =>   true,
]);

//  Host name.
$app->Unit('selftest')->Config()->Set('column', [
	'name'    => 'host',
	'type'    => 'text',
	'null'    =>  false,
	'collate' => 'ascii_general_ci',
	'comment' => 'host name.',
]);

//  Timestamp.
$app->Unit('selftest')->Config()->Set('column', [
	'name'    => 'timestamp',
	'type'    => 'timestamp',
	'comment' => 'On update current timestamp.',
]);

//  Auto incrment id.
$app->Unit('selftest')->Config()->Set('index', [
	'name'    => 'ai',
	'type'    => 'ai',
	'column'  => 'ai',
	'comment' => 'auto incrment',
]);

//  Search unique index key.
$app->Unit('selftest')->Config()->Set('index', [
	'name'    => 'hash',
	'type'    => 'unique',
	'column'  => 'hash',
	'comment' => 'unique index key',
]);
