<?php
	define('SYSPATH', true);

	function test_autoloader ( $name ) {
		$base = dirname( __FILE__ ) . '/../classes/';
		$parts = explode( '_', strtolower( $name ) );
		$path = $base . implode( '/', $parts ) . '.php';
		if( file_exists( $path ) ) {
			include( $path );
			return true;
		}
		else {
			return false;
		}
	}

	spl_autoload_register( "test_autoloader" );
