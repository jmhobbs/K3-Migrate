<?php defined('SYSPATH') or die('No direct script access.');

Route::set('migrate', 'migrate(/<action>(/<id>))',
    array(
        'action' => '(index|up|down)',
        'id' => '\d+',
    ))->defaults(array(
        'controller'=> 'migrate',
        'action' => 'index',
    ));
