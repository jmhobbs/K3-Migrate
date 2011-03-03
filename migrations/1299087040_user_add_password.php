<?php

	class UserAddPassword extends Migration {
		public function up () {
			$table = &self::ChangeTable( 'User' );
			$table->addColumn( 'string', 'password' );
		}

		public function down () {
			$table = &self::ChangeTable( 'User' );
			$table->removeColumn( 'password' );
		}
	}