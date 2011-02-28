<?php

        class Migration_Statement_DropTable extends Migration_Statement {

                protected $_tableName;

		public function __construct( $tableName ) {
			$this->_tableName = $tableName;
		}

		public function toSQL () {
			return "DROP TABLE `{$this->_tableName}`;\n";
		}

	}
