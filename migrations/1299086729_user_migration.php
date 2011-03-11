<?php

	class UserMigration extends Migration {
		public function up () {
			$table = &self::CreateTable( 'User' );
			$table->addColumn(
				'string',
				'first_name',
				array(
					'comment' => 'The users first name.'
				)
			);
			$table->addColumn(
				'string',
				'last_name'
			);
			$table->addColumn(
				'string',
				'username',
				array( 'null' => false )
			);
			$table->addColumn(
				'string',
				'email',
				array( 'null' => false )
			);
			$table->addColumn(
				'text',
				'bio'
			);
			$table->addColumn(
				'timestamp',
				'created'
			);
			$table->addColumn(
				'float',
				'number'
			);
			$table->addIndex( array( 'email' => array() ), array( 'unique' ) );
		}

		public function down () {
			$table = &self::DropTable( 'User' );
		}
	}
