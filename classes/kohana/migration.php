<?php

	abstract class Kohana_Migration {

		protected static $statements = array();

		/**
		 * @abstract
		 * The code required to migrate up.
		 */
		protected abstract function up ();

		/**
		 * @abstract
		 * The code required to migrate down.
		 */
		protected abstract function down ();

		/**
		 * Generate the SQL for a migration up.
		 * Does not run before/after hooks.
		 * @return string
		 */
		public function queryUp () {
			self::$statements = array();
			$this->up();
			return $this->toSQL();
		}

		/**
		 * Generate the SQL for a migration down.
		 * Does not run before/after hooks.
		 * @return string
		 */
		public function queryDown () {
			self::$statements = array();
			$this->down();
			return $this->toSQL();
		}

		/**
		 * Execute a migration up.
		 * @throws Exception - On migration errors.
		 * @param Database $database - The Database object. API compatible to http://kohanaframework.org/3.1/guide/api/Database
		 */
		public function migrateUp ( $database ) {
			self::$statements = array();
			$this->up();
			$this->migrate( $database );
		}

		/**
		 * Execute a migration down.
		 * @throws Exception - On migration errors.
		 * @param Database $database - The Database object. API compatible to http://kohanaframework.org/3.1/guide/api/Database
		 */
		public function migrateDown ( $database ) {
			self::$statements = array();
			$this->down();
			$this->migrate( $database );
		}

		/**
		 * Generate SQL for the current statement queue.
		 * @return string - String of SQL statements.
		 */
		protected function toSQL () {
			$sql = '';
			foreach( self::$statements as $statement ) {
				$sql .= "\n" . $statement->toSQL();
			}
			return $sql;
		}

		/**
		 * Execute migration of the current statement queue.
		 * @param Database $database - The Database object. API compatible to http://kohanaframework.org/3.1/guide/api/Database
		 * @throws Exception - On migration errors.
		 */
		protected function migrate ( $database ) {
			try {
				$database->begin();
				foreach( self::$statements as $statement ) {
					if( ! $statement->before( $this, $database ) ) { throw new Exception ( "Before Hook Returned False" ); }
					$database->query( $database::INSERT, $statement->toSQL() );
					if( ! $statement->after( $this, $database ) ) { throw new Exception ( "After Hook Returned False" ); }
				}
				$database->commit();
			}
			catch ( Exception $e ) {
				$database->rollback();
				throw $e;
			}
		}

		/**
		 * Add a CreateTable Statement to the statement queue.
		 *
		 * @static
		 * @param string $name - The name of the table
		 * @param array|null $args - Optional arguments array for table creation
		 * @return Kohana_Migration_Statement_CreateTable
		 */
		public static function &CreateTable ( $name, $args = null ) {
			self::$statements[] = new Kohana_Migration_Statement_CreateTable( $name, $args );
			return self::$statements[count(self::$statements)-1];
		}

		/**
		 * Add a ChangeTable Statement to the statement queue.
		 * @static
		 * @param string $name - The name of the table
		 * @return Kohana_Migration_Statement_ChangeTable
		 */
		public static function &ChangeTable ( $name ) {
			self::$statements[] = new Kohana_Migration_Statement_ChangeTable( $name );
			return self::$statements[count(self::$statements)-1];
		}

		/**
		 * Alias for Kohana_Migration::ChangeTable.
		 * @static
		 * @param $name - The name of the table.
		 * @return Kohana_Migration_Statement_ChangeTable
		 */
		public static function &AlterTable ( $name ) {
			return self::ChangeTable( $name );
		}

		/**
		 * Add a DropTable statement to the statement queue.
		 * 		 * @static
		 * @param $name - The name of the table
		 * @return Kohana_Migration_Statement_DropTable
		 */
		public static function &DropTable ( $name ) {
			self::$statements[] = new Kohana_Migration_Statement_DropTable( $name );
			return self::$statements[count(self::$statements)-1];
		}

	}
