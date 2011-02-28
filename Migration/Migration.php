<?php

	abstract class Migration {

		protected static $statements = array();

		protected abstract function up ();
		protected abstract function down ();
		
		public function queryUp () {
			self::$statements = array();
			$this->up();
			return $this->toSQL();
		}

		public function queryDown () {
			self::$statements = array();
			$this->down();
			return $this->toSQL();
		}

		protected function toSQL () {
                        $sql = '';
                        foreach( self::$statements as $statement ) {
                                $sql .= "\n" . $statement->toSQL();
                        }
                        return $sql;
		}

		public static function &CreateTable ( $name, $args = null ) {
			self::$statements[] = new Migration_Statement_CreateTable( $name, $args );
			return self::$statements[count(self::$statements)-1];
		}

		public static function &DropTable ( $name ) {
			self::$statements[] = new Migration_Statement_DropTable( $name );
			return self::$statements[count(self::$statements)-1];
		}
	}
