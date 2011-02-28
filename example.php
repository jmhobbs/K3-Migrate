<?php
	require_once( 'Migration/include.php' );

	class UserMigration extends Migration {
		public function up () {
			$table = &self::CreateTable( 'User', array( 'modified' => false ) );
			$table->addColumn( 'string', 'name', array( 'default' => 'John' ) );
		}

		public function down () {
			$table = &self::DropTable( 'User' );
		}
	}

	//////// Runner Code ////////
	$migration = new UserMigration();
	print "== UP ==\n";
	print $migration->queryUp();
	print "\n== DOWN ==\n";
	print $migration->queryDown();
