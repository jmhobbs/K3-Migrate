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
		protected $_removeForeignKeys = array();
		protected $_addPrimaryKey = false;
		protected $_removePrimaryKey = false;
		protected $_renameTo = '';

		public function __construct( $tableName ) {
			$this->_tableName = $tableName;
		}

		/**
		 * Add a key to this table.
		 *
		 * @see Kohana_Migration_Key
		 *
		 * @param array $columns - An array of columns to put the key on.
		 * @param array|null $traits - An optional array of traits for the key.
		 */
		public function addKey ( $columns, $traits = null ) {
			$key = new Kohana_Migration_Key($columns, $traits);
			$this->_addKeys[$key->getName()] = $key;
		}

		/**
		 * Remove a key from this table.
		 * @param string $name - The name of the key.
		 */
		public function removeKey ( $name ) {
			$this->_removeKeys[] = $name;
		}

		/**
		 * Remove a key from this table by it's definition.
		 * This only works when you have added the key without specifying the name.
		 * @see Kohana_Migration_Key
		 * @param array $columns - An array of columns to put the key on.
		 * @param array|null $traits - An optional array of traits for the key.
		 */
		public function removeKeyByDefinition ( $columns, $traits = null ) {
			$key = new Kohana_Migration_Key($columns, $traits);
			$this->_removeKeys[] = $key->getName();
		}

		/**
		 * Add a foreign key to this table.
		 * @see Kohana_Migration_Key_Foreign::$_traits
		 * @param array $near_columns - An array of columns in the near table to match to foreign columns.
		 * @param string $far_table - The name of the foreign table.
		 * @param array $far_columns - An array with a 1:1 matching of column names on the foreign table.
		 * @param array|null $traits - An optional array of traits to apply to this table.
		 */
		public function addForeignKey ( $near_columns, $far_table, $far_columns, $traits = null ) {
			$key = new Kohana_Migration_Key_Foreign($near_columns, $far_table, $far_columns, $traits);
			$this->_addKeys[$key->getName()] = $key;
		}

		/**
		 * Remove a foreign key from this table by it's name
		 * @param string $name - The name of the index.
		 */
		public function removeForeignKey ( $name ) {
			$this->_removeForeignKeys[] = $name;
		}

		/**
		 * Remove a foreign key from this table by it's definition.
		 * This only works when you have added the key without specifying the name.
		 * @see Kohana_Migration_Key_Foreign::$_traits
		 * @param array near_columns - An array of columns in the near table to match to foreign columns.
		 * @param string far_table - The name of the foreign table.
		 * @param array far_columns - An array with a 1:1 matching of column names on the foreign table.
		 * @param array|null traits - An optional array of traits to apply to this table.
		 */
		public function removeForeignKeyByDefinition ( $near_columns, $far_table, $far_columns, $traits = null ) {
			$key = new Kohana_Migration_Key_Foreign($near_columns, $far_table, $far_columns, $traits);
			$this->_removeForeignKeys[] = $key->getName();
		}

		/**
		 * Add a primary key to table
		 * @param array|string $columns
		 */
		public function addPrimaryKey ( $columns ) {
			$this->_addPrimaryKey = (array) $columns;
		}

		/**
		 * Remove a primary key from this table
		 * AUTO_INCREMENT for key field must be removed before
		 * @example:
		 * $table = self::ChangeTable('test');
		 * $table->alterColumn( 'id', 'integer', array( 'size' => 11, 'null' => false, 'unsigned' => true ) );
		 * $table->removePrimaryKey();
		 */
		public function removePrimaryKey () {
			$this->_removePrimaryKey = true;
		}

		/**
		 * Add an index to this table.
		 * @see Kohana_Migration_Index
		 * @param array columns - An array of columns to put the inex on.
		 * @param array|null traits - An optional array of traits for the index.
		 */
		public function addIndex ( $columns, $traits = null ) {
			$index = new Kohana_Migration_Index($columns, $traits);
			$this->_addIndexes[$index->getName()] = $index;
		}

		/**
		 * Remove an index from this table.
		 * @param string $name - The name of the index.
		 */
		public function removeIndex( $name ) {
			$this->_removeIndexes[] = $name;
		}

		/**
		 * Remove an index from this table by it's definition.
		 * This only works when you have added the index without specifying the name.
		 * @see Kohana_Migration_Index
		 * @param array columns - An array of columns to put the index on.
		 * @param array|null traits - An optional array of traits for the index.
		 */
		public function removeIndexByDefinition( $columns, $traits = null ) {
			$index = new Kohana_Migration_Index($columns, $traits);
			$this->_removeIndexes[] = $index->getName();
		}

		/**
		 * Add a column to this table.
		 * @see Kohana_Migration_Column::$_traits
		 * @param string type - The type of column to add.
		 * @param string name - The name of the new column.
		 * @param array|null traits - An optional array of traits for this column.
		 */
		public function addColumn ( $type, $name, $traits = null ) {
			$this->_addColumns[$name] = new Kohana_Migration_Column( $name, $type, $traits );
		}

		/**
		 * Remove a column from this table.
		 * @param string $name - The name of the column to remove.
		 */
		public function removeColumn ( $name ) {
			$this->_removeColumns[] = $name;
		}

		/**
		 * Alter a column.
		 * @see Kohana_Migration_Column::$_traits
		 * @param string name - The original name of the column.
		 * @param string type - The type of the column.
		 * @param array|null traits - An optional array of traits for this column.
		 * @param string|null new_name - An optional new name for this column.
		 */
		public function alterColumn ( $name, $type, $traits = null, $new_name = null ) {
			if( is_null( $new_name ) or $name == $new_name ) {
				$this->_modifyColumns[$name] =  new Kohana_Migration_Column( $name, $type, $traits );
			}
			else {
				$this->_changeColumns[$name] = new Kohana_Migration_Column( $new_name, $type, $traits );
			}
		}

		/**
		 * Rename a table
		 * @param string name - New name for table
		 */
		public function renameTo( $name ) {
			$this->_renameTo = $name;
		}

		/**
		 * Generate SQL statement
		 * @todo All operations in order of request
		 * @return string
		 */
		public function toSQL () {
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
			foreach( $this->_removeForeignKeys as $name) {
				$alters[] = 'DROP FOREIGN KEY ' . $name;
			}
			if( $this->_renameTo ) {
				$alters[] = 'RENAME TO ' . $this->_renameTo;
			}
			if( $this->_removePrimaryKey) {
				$alters[] = 'DROP PRIMARY KEY';
			}
			if( $this->_addPrimaryKey !== false ) {
				$alters[] = sprintf('ADD PRIMARY KEY (%s)', implode(',', $this->_addPrimaryKey));
			}
			return $sql . implode( ",\n  ", $alters ) . ";\n";
		}

		/**
		 * @todo Merge this with CreateTable
		 */
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

		/**
		 * @todo Can we select between columns and keys/indices?
		 */
		public function __unset ( $name ) {
			$this->_removeColumns[] = $name;
		}

	}
