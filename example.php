<?php

	require_once( 'Migration/include.php' );

	class UserMigration extends Migration {
		public function up () {
			$table = self::CreateTable( 'User', array( 'modified' => false ) );
			$table->addColumn( 'string', 'name', array( 'default' => 'John' ) );
			return $table->toSQL();
		}

		public function down () {
			return "";
		}
	}

	//////// Runner Code ////////
	$migration = new UserMigration();
	die( $migration->up() );