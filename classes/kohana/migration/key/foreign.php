<?php

	/*!
		Statement representative of a Foreign Key.

\verbatim
[CONSTRAINT [symbol]] FOREIGN KEY
    [index_name] (index_col_name, ...)
    REFERENCES tbl_name (index_col_name,...)
    [ON DELETE reference_option]
    [ON UPDATE reference_option]

reference_option:
    RESTRICT | CASCADE | SET NULL | NO ACTION
\endverbatim
	*/

	class Kohana_Migration_Key_Foreign extends Kohana_Migration_Statement {

		protected $_name;
		protected $_index_name;
		protected $_near_columns;
		protected $_far_columns;
		protected $_far_table;
		/*!
			Traits of the statement. Can be overridden in the constructor.

			\param name The name of the constraint. If null, no constraint is named.
			\param index_name The name of the index on the near table. If null, an index name is generated.
			\param update The referential action to take on foreign updates. If null, no action is taken.
			\param delete The referential action to take on foreign deletes. If null, no action is taken.

			\sa $referentialActions
		*/
		protected $_traits = array(
			'name' => null,
			'index_name' => null,
			'update' => null,
			'delete' => null,
		);
	
		/*!
			Possible actions to take on update or delete.

			\sa $_traits
		*/
		protected $referentialActions = array(
			'CASCADE',
			'SET NULL',
			'NO ACTION',
			'RESTRICT',
		);

		/*!
			\param near_columns An array of columns in the near table to match to foreign columns.
			\param far_table The name of the foreign table.
			\param far_columns An array with a 1:1 matching of column names on the foreign table.
			\param traits An optional array of traits to apply to this table.

			\sa $_traits
		*/
		public function __construct ( $near_columns, $far_table, $far_columns, $traits = null ) {
			$this->_near_columns = $near_columns;
			$this->_far_columns = $far_columns;
			$this->_far_table = $far_table;

			if( is_array( $traits ) ) {
				$this->_traits = array_merge( $this->_traits, $traits );
			}

			$this->_index_name = $this->_traits['index_name'];

			// Get the name, if needed
			if( is_null( $this->_traits['name'] ) ) {
				$this->_name = $this->_generateName();
			}
			else {
				$this->_name = $this->_traits['name'];
			}
		}

		/*!
			Generates a predictable name representative of this key.

			\returns A String representation of this key.
		*/
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
				if( ! is_null( $this->_traits['delete'] ) ) {
					if( in_array( strtoupper( $this->_traits['delete'] ), $this->referentialActions ) ) {
						$blocks[] = 'ON DELETE ' . strtoupper( $this->_traits['delete'] );
					}
				}

				if( ! is_null( $this->_traits['update'] ) ) {
					if( in_array( strtoupper( $this->_traits['update'] ), $this->referentialActions ) ) {
						$blocks[] = 'ON UPDATE ' . strtoupper( $this->_traits['update'] );
					}
				}
			}

			return implode( ' ', $blocks );
		}

		/*!
			Get the current name of this statement.

			\returns A string name for this statement.
		*/
		public function getName () { return $this->_name; }

	}

