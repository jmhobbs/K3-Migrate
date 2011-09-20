<?php

	abstract class Kohana_Migration_Statement {
		protected $_before = array();
		protected $_after = array();

		abstract public function toSQL ();

		public function runBefore ( $fn ) {
			$this->_before[] = $fn;
		}

		public function before ( $migration, $database ) {
			foreach( $this->_before as $before ) {
				if( ! $before( $this, $migration, $database ) ) {
					return false;
				}
			}
			return true;
		}

		public function runAfter ( $fn ) {
			$this->_after[] = $fn;
		}

		public function after ( $migration, $database ) {
			foreach( $this->_after as $after ) {
				if( ! $after( $this, $migration, $database ) ) {
					return false;
				}
			}
			return true;
		}

	}
