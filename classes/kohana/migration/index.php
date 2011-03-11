<?php

	/*
	CREATE [UNIQUE|FULLTEXT|SPATIAL] INDEX index_name
	    [index_type]
	    ON tbl_name (index_col_name,...)
	    [index_type]

	index_col_name:
	    col_name [(length)] [ASC | DESC]

	index_type:
	    USING {BTREE | HASH}
	*/


	class Kohana_Migration_Index extends Kohana_Migration_Statement {

		protected $_name;
		protected $_columns;
		protected $_table;
		protected $_traits;

		protected static $variantTraits = array(
			'unique' => 'UNIQUE',
			'fulltext' => 'FULLTEXT',
			'spatial' => 'SPATIAL'
		);
		protected static $typeTraits = array(
			'btree' => 'USING BTREE',
			'hash' => 'USING HASH'
		);

		public function __construct ( $table, $columns, $traits = null ) {

			$this->_columns = $columns;
			$this->_table = $table;

			$default_traits = array(
				'name' => null
			);

			// Set up traits
			if( is_array( $traits ) ) {
				$this->_traits = array_merge( $default_traits, $traits );
			}
			else {
				$this->_traits = $default_traits;
			}

			// Get the name, if needed
			if( is_null( $this->_traits['name'] ) ) {
				$this->_name = "index_" . implode('_', array_keys($this->_columns));
			}
			else {
				$this->_name = $this->_traits['name'];
			}

		}

		public function toSQL () {
			$variant = '';
			foreach( $this->_traits as $trait ) {
				if( array_key_exists( $trait, self::$variantTraits ) ) {
					$variant = self::$variantTraits[$trait];
				}
			}

			$type = '';
			foreach( $this->_traits as $trait ) {
				if( array_key_exists( $trait, self::$typeTraits ) ) {
					$type = self::$typeTraits[$trait];
				}
			}

			$sql = "$variant INDEX `{$this->_name}` ON `{$this->_table}` (";

			$keys = array();
			foreach( $this->_columns as $column => $traits ) {
				$key = "`{$column}` ";
				if( isset( $traits['length'] ) ) {
					$key .= '(' . intval( $traits['length'] ) . ') ';
				}
				if( isset( $traits['order'] ) ) {
					if( $traits['order'] == 'asc' ) {
						$key .= 'ASC ';
					}
					else if( $traits['order'] == 'desc' ) {
						$key .= 'DESC ';
					}
				}
				$keys[] = trim( $key );
			}
			$sql .= implode( ', ', $keys ) . ") $type";

			return $sql;
		}

		public function getName () { return $this->_name; }
	}