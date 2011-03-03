<?php

	class Migration_Statement_ChangeTable extends Migration_Statement {

		protected $_tableName;
		protected $_engine;
		protected $_charset;
		protected $_addColumns = array();
		protected $_removeColumns = array();

		public function __construct( $tableName ) {
			$this->_tableName = $tableName;
		}

		public function addColumn ( $type, $name, $traits = null ) {
			$this->_addColumns[$name] = new Migration_Column( $name, $type, $traits );
		}

		public function removeColumn ( $name ) {
			$this->_removeColumns[] = $name;
		}

		public function toSQL () {
			$sql = "ALTER TABLE `{$this->_tableName}` ";
			$alters = array();
			foreach( $this->_addColumns as $column ) {
				$alters[] = 'ADD COLUMN ' . $column->toSQL();
			}
			foreach( $this->_removeColumns as $column ) {
				$alters[] = 'DROP COLUMN `' . $column . '`';
			}

			return $sql . implode( ', ', $alters ) . ";\n";
		}

	}
