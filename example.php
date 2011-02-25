<?php

	require_once( 'Migration/include.php' );

	$table = Migration::CreateTable( 'User', array( 'modified' => false ) );
	$table->addColumn( 'string', 'name', array( 'default' => 'John' ) );
	die( $table->toSQL() );