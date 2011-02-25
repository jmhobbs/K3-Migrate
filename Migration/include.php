<?php

	function __migration_autoloader ( $name ) {
		$path = dirname( __FILE__ ) . '/'. implode( '/', explode( '_', $name ) ) . '.php';
		if( file_exists( $path ) ) {
			require_once( $path );
			return true;
		}
		return false;
	}

	spl_autoload_register( '__migration_autoloader' );