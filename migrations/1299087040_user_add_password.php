<?php

	class UserAddPassword extends Migration {
		public function up () {
			$table = &self::ChangeTable( 'User' );
			$table->addColumn( 'string', 'password' );
			$table->addIndex( array( 'email' => array() ), array( 'unique' ) );
			$table->removeIndex( 'email_index' );
		}

		public function down () {
			$table = &self::ChangeTable( 'User' );
			$table->removeColumn( 'password' );
		}
	}