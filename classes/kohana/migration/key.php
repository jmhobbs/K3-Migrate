<?php

	/*
		?
	*/

	class Kohana_Migration_Key extends Kohana_Migration_Index {

		public function _generateName () {
			return "key_" . implode('_', array_keys($this->_columns));
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

			$sql = trim( "$variant KEY `{$this->_name}` (" );

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
			$sql .= implode( ', ', $keys ) . ")" . ( ( empty( $type ) ) ? '' : " $type" );

			return $sql;
		}

	}
