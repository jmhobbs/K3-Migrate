<?php
	require_once( 'Migration/include.php' );

	$runner = new Migration_Manager('migrations');

	foreach( $runner->enumerateMigrations() as $migration ) {
		print "==[ $migration UP ]==\n";
		print $runner->runMigrationUp( $migration );
		print "\n";
		print "==[ $migration DOWN ]==\n";
		print $runner->runMigrationDown( $migration );
		print "\n";
	}
