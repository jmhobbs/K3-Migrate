<?php defined('SYSPATH') or die('No direct script access.');

Route::set('migrate', 'migrate(/<action>(/<id>))', array(
	'action' => '(index|up|down|print|seed|create)',
	'id' => '[A-Za-z]+',
))->defaults(array(
	'controller' => 'migrate',
	'action' => 'index',
));
