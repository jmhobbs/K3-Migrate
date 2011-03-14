<?php

	class Kohana_Migration_Statement_CreateTable extends Kohana_Migration_Statement {

		protected $_tableName;
		protected $_engine;
		protected $_charset;

		protected $_columns = array();
		protected $_primaryKey = null;

		/**
		* Options:
		*      id  => false/string   - Do not create an automatic id column
		* created  => false/string   - Created column name || false == no column
		* modified => false/string   - Modified column name || false == no column
		*   engine => string
		*  charset => string
		*/
		public function __construct ( $tableName, $args = null ) {
			$this->_tableName = $tableName;

			$defaults = array(
				'id'       => 'id',
				'created'  => 'created',
				'modified' => 'modified',
				'engine'   => 'InnoDB',
				'charset'  => 'utf8',
			);

			if( is_array( $args ) ) { $args = array_merge( $defaults, $args ); }
			else { $args = $defaults; }

			if( false !== $args['id'] ) {
				$this->addColumn( 'integer', $args['id'], array( 'null' => false, 'unsigned' => true, 'auto_increment' => true ) );
				$this->primaryKey( $args['id'] );
			}

			if( false !== $args['created'] ) {
				$this->addColumn( 'integer', $args['created'], array( 'null' => false, 'unsigned' => true ) );
			}

			if( false !== $args['modified'] ) {
				$this->addColumn( 'integer', $args['modified'], array( 'null' => false ) );
			}

			$this->engine( $args['engine'] );
			$this->charset( $args['charset'] );
		}

		public function toSQL () {
			$sql = "CREATE TABLE `{$this->_tableName}` (\n\t";

			$rows = array();

			foreach( $this->_columns as $column ) {
				$rows[] = $column->toSQL();
			}

			if( ! is_null( $this->_primaryKey ) ) {
				$rows[] = "PRIMARY KEY( `{$this->_primaryKey}` )";
			}

			$sql .= implode( ",\n\t", $rows );

			$sql .= "\n) ENGINE={$this->_engine} DEFAULT CHARSET={$this->_charset};\n\n";

			foreach( $this->_indexes as $name => $index ) {
				$sql .= 'CREATE ' . $index->toSQL() . ";\n";
			}

			return $sql;
		}

		public function engine ( $engine ) { $this->_engine = $engine; }
		public function charset ( $charset ) { $this->_charset = $charset; }
		public function primaryKey ( $columnName ) { $this->_primaryKey = $columnName; }
		public function tableName ( $tableName ) { $this->_tableName = $tableName; }

		public function addColumn ( $type, $name, $traits = null ) {
			$this->_columns[$name] = new Kohana_Migration_Column( $name, $type, $traits );
		}

		public function addIndex ( $columns, $traits = null ) {
			$index = new Kohana_Migration_Index($this->_tableName, $columns, $traits);
			$this->_indexes[$index->getName()] = $index;
		}

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
	}
