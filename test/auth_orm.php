<?php

	require_once( 'autoloader.php' );
	require_once( 'mock/database.php' );
	require_once( '../examples/authorm/1300290077_auth_o_r_m.php' );

	$database = new Mock_Database();

	$migration = new AuthORM();

	print "===[ UP ]===================\n";
	$migration->migrateUp( $database );
	print "\n\n\n";

	print "===[ DOWN ]=================\n";
	$migration->migrateDown( $database );
	print "\n\n\n";
