<?php
	require_once( 'Migration/include.php' );

	class UserMigration extends Migration {
		public function up () {
			$table = &self::CreateTable( 'User', array( 'modified' => false ) );
			$table->addColumn( 'string', 'name', array( 'default' => 'John', 'comment' => 'The users first name.' ) );
			$table->addIndex( array( 'name' => array( 'length' => 10 ), 'id' => array() ), array( 'unique' ) );
		}

		public function down () {
			$table = &self::DropTable( 'User' );
		}
	}

	//////// Runner Code ////////
	$migration = new UserMigration();
	print "== UP ==\n";
	print $migration->queryUp();
	print "\n\n== DOWN ==\n";
	print $migration->queryDown();
