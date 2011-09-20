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
		protected $_addKeys = array();
		protected $_removeIndexes = array();
		protected $_removeKeys = array();

		public function __construct( $tableName ) {
			$this->_tableName = $tableName;
		}

		/*!
			Add a key to this table.

			\param columns An array of columns to put the key on.
			\param traits An optional array of traits for the key.

			\sa Kohana_Migration_Key
		*/
		public function addKey ( $columns, $traits = null ) {
			$key = new Kohana_Migration_Key($columns, $traits);
			$this->_addKeys[$key->getName()] = $key;
		}

		/*!
			Remove a key from this table.

			\param name The name of the key.
		*/
		public function removeKey ( $name ) {
			$this->_removeKeys[] = $name;
		}

		/*!
			Remove a key from this table by it's definition.

			This only works when you have added the key without specifying the name.

			\param columns An array of columns to put the key on.
			\param traits An optional array of traits for the key.

			\sa Kohana_Migration_Key
		*/
		public function removeKeyByDefinition( $columns, $traits = null ) {
			$key = new Kohana_Migration_Key($columns, $traits);
			$this->_removeKeys[] = $key->getName();
		}

		/*!
			Add a foreign key to this table.

			\param near_columns An array of columns in the near table to match to foreign columns.
			\param far_table The name of the foreign table.
			\param far_columns An array with a 1:1 matching of column names on the foreign table.
			\param traits An optional array of traits to apply to this table.

			\sa Kohana_Migration_Key_Foreign::$_traits
		*/
		public function addForeignKey ( $near_columns, $far_table, $far_columns, $traits = null ) {
			$key = new Kohana_Migration_Key_Foreign($near_columns, $far_table, $far_columns, $traits);
			$this->_addKeys[$key->getName()] = $key;
		}

		/*!
			Remove a foreign key from this table by it's definition.

			This only works when you have added the key without specifying the name.

			\param near_columns An array of columns in the near table to match to foreign columns.
			\param far_table The name of the foreign table.
			\param far_columns An array with a 1:1 matching of column names on the foreign table.
			\param traits An optional array of traits to apply to this table.

			\sa Kohana_Migration_Key_Foreign::$_traits
		*/
		public function removeForeignKeyByDefinition ( $near_columns, $far_table, $far_columns, $traits = null ) {
			$key = new Kohana_Migration_Key_Foreign($near_columns, $far_table, $far_columns, $traits);
			$this->_removeKeys[] = $key->getName();
		}

		/*!
			Add an index to this table.

			\param columns An array of columns to put the inex on.
			\param traits An optional array of traits for the index.

			\sa Kohana_Migration_Index
		*/
		public function addIndex ( $columns, $traits = null ) {
			$index = new Kohana_Migration_Index($columns, $traits);
			$this->_addIndexes[$index->getName()] = $index;
		}

		/*!
			Remove an index from this table.

			\param name The name of the index.
		*/
		public function removeIndex( $name ) {
			$this->_removeIndexes[] = $name;
		}

		/*!
			Remove an index from this table by it's definition.

			This only works when you have added the index without specifying the name.

			\param columns An array of columns to put the index on.
			\param traits An optional array of traits for the index.

			\sa Kohana_Migration_Index
		*/
		public function removeIndexByDefinition( $columns, $traits = null ) {
			$index = new Kohana_Migration_Index($columns, $traits);
			$this->_removeIndexes[] = $index->getName();
		}

		/*!
			Add a column to this table.

			\param type The type of column to add.
			\param name The name of the new column.
			\param traits An optional array of traits for this column.

			\sa Kohana_Migration_Column::$_traits
		*/
		public function addColumn ( $type, $name, $traits = null ) {
			$this->_addColumns[$name] = new Kohana_Migration_Column( $name, $type, $traits );
		}

		/*!
			Remove a column from this table.

			\param name The name of the column to remove.
		*/
		public function removeColumn ( $name ) {
			$this->_removeColumns[] = $name;
		}

		/*!
			Alter a column.

			\param name The original name of the column.
			\param type The type of the column.
			\param traits An optional array of traits for this column.
			\param new_name An optional new name for this column.

			\sa Kohana_Migration_Column::$_traits
		*/
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
				$alters[] = 'ADD ' . $index->toSQL();
			}
			foreach( $this->_removeIndexes as $name) {
				$alters[] = 'DROP ' . $name;
			}
			foreach( $this->_addKeys as $name => $index ) {
				$alters[] = 'ADD ' . $index->toSQL();
			}
			foreach( $this->_removeKeys as $name) {
				$alters[] = 'DROP KEY ' . $name;
			}
			return $sql . implode( ",\n  ", $alters ) . ";\n";
		}

		// TODO: Merge this with CreateTable
		public function __set ( $type, $value ) {
			if( Kohana_Migration_Column::isType($type) ) {
				if( is_array( $value ) ) {
					// $t->integer = array( 'name', array( 'size' => 10 ) );
					$name = array_shift( $value );
					$this->addColumn( $type, $name, $value );
				}
				else {
					// $t->integer = 'name';
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
			// TODO: Can we select between columns and keys/indices?
			$this->_removeColumns[] = $name;
		}

	}
