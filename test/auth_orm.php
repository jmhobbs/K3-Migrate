<?php

	require_once( 'autoloader.php' );
	require_once( '../examples/authorm/1300290077_auth_o_r_m.php' );

	$migration = new AuthORM();

	print "===[ UP ]===================\n";
	print $migration->queryUp();
	print "\n\n\n";

	print "===[ DOWN ]=================\n";
	print $migration->queryDown();
	print "\n\n\n";
