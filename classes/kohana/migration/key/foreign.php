<?php

	/*
		[CONSTRAINT [symbol]] FOREIGN KEY
				[index_name] (index_col_name, ...)
				REFERENCES tbl_name (index_col_name,...)
				[ON DELETE reference_option]
				[ON UPDATE reference_option]

		reference_option:
				RESTRICT | CASCADE | SET NULL | NO ACTION
	*/

	class Kohana_Migration_Key_Foreign extends Kohana_Migration_Statement {

		protected $_name;
		protected $_near_columns;
		protected $_far_columns;
		protected $_far_table;
		protected $_traits = array(
			'name' => null,
			'index_name' => null,
		);

		protected $referentialActions = array(
			'CASCADE',
			'SET NULL',
			'NO ACTION',
			'RESTRICT',
		);

		public function __construct ( $near_columns, $far_table, $far_columns, $traits = null ) {
			$this->_near_columns = $near_columns;
			$this->_far_columns = $far_columns;
			$this->_far_table = $far_table;

			if( is_array( $traits ) ) {
				$this->_traits = array_merge( $this->_traits, $traits );
			}

			// Get the name, if needed
			if( is_null( $this->_traits['name'] ) ) {
				$this->_name = $this->_generateName();
			}
			else {
				$this->_name = $this->_traits['name'];
			}
		}

		public function _generateName () {
			return "ibfk_" . implode('_', $this->_near_columns);
		}

		public function toSQL () {

			$blocks = array();

			if( ! is_null( $this->_traits['name'] ) ) {
				$blocks[] = "CONSTRAINT `{$this->_name}`";
			}

			$blocks[] = "FOREIGN KEY" . ( ( isset( $this->_index_name ) and ! is_null( $this->_index_name ) ) ? " `{$this->_index_name}`" : '' ) . " ( `" . implode( '`, `', $this->_near_columns ) . "` )";
			$blocks[] = "REFERENCES `{$this->_far_table}` ( `" . implode( '`, `', $this->_far_columns ) . "` )";

			if ( is_array( $this->_traits ) ) {
				if( isset( $this->_traits['delete'] ) ) {
					if( in_array( strtoupper( $this->_traits['delete'] ), $this->referentialActions ) ) {
						$blocks[] = 'ON DELETE ' . strtoupper( $this->_traits['delete'] );
					}
				}

				if( isset( $this->_traits['update'] ) ) {
					if( in_array( strtoupper( $this->_traits['update'] ), $this->referentialActions ) ) {
						$blocks[] = 'ON UPDATE ' . strtoupper( $this->_traits['update'] );
					}
				}
			}

			return implode( ' ', $blocks );
		}

		public function getName () { return $this->_name; }

	}

