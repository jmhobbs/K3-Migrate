<?php

	class Kohana_Migration_Statement_ChangeTable extends Kohana_Migration_Statement {

		protected $_tableName;
		protected $_engine;
		protected $_charset;
		protected $_addColumns = array();
		protected $_removeColumns = array();
		// Change == Rename
		protected $_changeColumns = array();
		// Modify == Change Definition
		protected $_modifyColumns = array();
		protected $_addIndexes = array();
		protected $_removeIndexes = array();

		public function __construct( $tableName ) {
			$this->_tableName = $tableName;
		}

		public function addIndex ( $columns, $traits = null ) {
			$index = new Kohana_Migration_Index($this->_tableName, $columns, $traits);
			$this->_addIndexes[$index->getName()] = $index;
		}

		public function removeIndexByDefinition( $columns, $traits = null ) {
			$index = new Kohana_Migration_Index($this->_tableName, $columns, $traits);
			$this->_removeIndexes[] = $index->getName();
		}

		public function removeIndex( $name ) {
			$this->_removeIndexes[] = $name;
		}

		public function addColumn ( $type, $name, $traits = null ) {
			$this->_addColumns[$name] = new Kohana_Migration_Column( $name, $type, $traits );
		}

		public function removeColumn ( $name ) {
			$this->_removeColumns[] = $name;
		}

		public function alterColumn ( $name, $type, $traits = null, $new_name = null ) {
			if( is_null( $new_name ) or $name == $new_name ) {
				$this->_modifyColumns[$name] =  new Kohana_Migration_Column( $name, $type, $traits );
			}
			else {
				$this->_changeColumns[$name] = new Kohana_Migration_Column( $new_name, $type, $traits );
			}
		}

		public function toSQL () {
			// TODO: All operations in order of request
			$sql = "ALTER TABLE `{$this->_tableName}`\n  ";
			$alters = array();
			foreach( $this->_addColumns as $column ) {
				$alters[] = 'ADD COLUMN ' . $column->toSQL();
			}
			foreach( $this->_removeColumns as $column ) {
				$alters[] = 'DROP COLUMN `' . $column . '`';
			}
			foreach( $this->_changeColumns as $name => $column ) {
				$alters[] = 'CHANGE COLUMN `' . $name . '` ' . $column->toSQL();
			}
			foreach( $this->_modifyColumns as $name => $column ) {
				$alters[] = 'MODIFY COLUMN ' . $column->toSQL();
			}
			foreach( $this->_addIndexes as $name => $index ) {
				$alters[] = 'ADD INDEX ' . $index->toSQL();
			}
			foreach( $this->_removeIndexes as $name) {
				$alters[] = 'DROP INDEX ' . $name;
			}

			return $sql . implode( ",\n  ", $alters ) . ";\n";
		}

		// TODO: Merge this with CreateTable
		public function __set ( $type, $value ) {
			if( Kohana_Migration_Column::isType($type) ) {
				if( is_array( $value ) ) {
					$name = array_shift( $value );
					$this->addColumn( $type, $name, $value );
				}
				else {
					$this->addColumn( $type, $value );
				}
			}
			else if ( Kohana_Migration_Index::isType($type) ) {
				if( is_array( $value ) ) {
					// $t->index = array( array( "column_name", "another_column" ), array( "btree" ) );
					if( 2 <= count( $value ) ) {
						$one = reset( $value );
						$two = next( $value );
						if( is_array( $one ) and is_array( $two ) ) {
							$this->addIndex( $one, array_merge( array( $type ), $two ) );
						}
						// $t->index = array( "column_name", array( "btree" ) );
						else if( is_array( $two ) ) {
							$this->addIndex( array( $one => null ), array_merge( array( $type ), $two ) );
						}
						// $t->index = array( "column_name", "another_column", "and_another" );
						else {
							$this->addIndex( $value, array( $type ) );
						}
					}
				}
				// $t->index = "column_name"
				else {
					$this->addIndex( array( $value => null), array( $type ) );
				}
			}
		}

		public function __unset ( $name ) {
			$this->_removeColumns[] = $name;
		}


	}
