<?php

	class Kohana_Migration_Statement_CreateTable extends Kohana_Migration_Statement {

		protected $_tableName;
		protected $_engine;
		protected $_charset;

		protected $_columns = array();
		protected $_indexes = array();
		protected $_keys = array();
		protected $_primaryKey = null;

		/**
		* Options:
		*         id  => false/string              - Do not create an automatic id column
		*    created  => false/string              - Created column name || false == no column
		*    modified => false/string              - Modified column name || false == no column
		* primary_key => true/false/string/array   - PK column name || true == auto || false == no PK || array == compound
		*      engine => string
		*     charset => string
		*/
		public function __construct ( $tableName, $args = null ) {
			$this->_tableName = $tableName;

			$defaults = array(
				'id'          => 'id',
				'created'     => 'created',
				'modified'    => 'modified',
				'primary_key' => true,
				'engine'      => 'InnoDB',
				'charset'     => 'utf8',
			);

			if( is_array( $args ) ) { $args = array_merge( $defaults, $args ); }
			else { $args = $defaults; }

			if( false !== $args['id'] ) {
				$this->addColumn( 'integer', $args['id'], array( 'size' => 11, 'null' => false, 'unsigned' => true, 'auto_increment' => true ) );
				if( true === $args['primary_key'] ) {
					$this->primaryKey( $args['id'] );
				}
			}

			if( false !== $args['created'] ) {
				$this->addColumn( 'integer', $args['created'], array( 'null' => false, 'unsigned' => true ) );
			}

			if( false !== $args['modified'] ) {
				$this->addColumn( 'integer', $args['modified'], array( 'null' => false ) );
			}

			if( is_string( $args['primary_key'] ) or is_array( $args['primary_key'] ) ) {
				$this->primaryKey( $args['primary_key'] );
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

			// Insert the PK
			if( ! is_null( $this->_primaryKey ) ) {
				$pk = ( is_array( $this->_primaryKey ) ) ? implode( '`, `', $this->_primaryKey ) : $this->_primaryKey; 
				$rows[] = "PRIMARY KEY( `$pk` )";
			}

			// Generate any other indexes/keys here
			foreach( $this->_keys as $name => $key ) {
				$rows[] = $key->toSQL();
			}
			foreach( $this->_indexes as $name => $index ) {
				$rows[] = $index->toSQL();
			}

			$sql .= implode( ",\n\t", $rows );

			$sql .= "\n) ENGINE={$this->_engine} DEFAULT CHARSET={$this->_charset};\n\n";


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
			$index = new Kohana_Migration_Index($columns, $traits);
			$this->_indexes[$index->getName()] = $index;
		}

		public function addKey ( $columns, $traits = null ) {
			$key = new Kohana_Migration_Key($columns, $traits);
			$this->_keys[$key->getName()] = $key;
		}

		public function addForeignKey ( $near_columns, $far_table, $far_columns, $traits = null ) {
			$key = new Kohana_Migration_Key_Foreign($near_columns, $far_table, $far_columns, $traits);
			$this->_keys[$key->getName()] = $key;
		}

		public function __set ( $type, $value ) {
			if( Kohana_Migration_Column::isType($type) ) {
				// $t->integer = array( 'name', 'option' => $value );
				if( is_array( $value ) ) {
					$name = array_shift( $value );
					$this->addColumn( $type, $name, $value );
				}
				// $t->integer = 'name';
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
