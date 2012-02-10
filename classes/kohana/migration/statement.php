<?php

	abstract class Kohana_Migration_Statement {

		protected $_before = array();
		protected $_after = array();

		/**
		 * Return the SQL required to execute this statement.
		 * @return string
		*/
		abstract public function toSQL ();

		/**
		 * Register a hook to run before the statement is executed.
		 * Only ran on top level statements (CreateTable, ChangeTable, DropTable)
		 * @param Closure|array $fn
		 */
		public function runBefore ( $fn ) {
			$this->_before[] = $fn;
		}

		/**
		 * Execute hooks registered for execution before query.
		 * @param string migration - The Kohana_Migration running this statement.
		 * @param Database database - The Database object the Kohana_Migration is using.
		 * @return bool, true if all hooks return true, false if any return false.
		*/
		public function before ( $migration, $database ) {
			foreach( $this->_before as $before ) {
				if( ! $before( $this, $migration, $database ) ) {
					return false;
				}
			}
			return true;
		}

		/**
		 * Register a hook to run after the statement is executed.
		 * Only ran on top level statements (CreateTable, ChangeTable, DropTable)
		 * @param Closure|array $fn
		*/
		public function runAfter ( $fn ) {
			$this->_after[] = $fn;
		}

		/**
		 * Execute hooks registered for execution after query.
		 * @param string migration - The Kohana_Migration running this statement.
		 * @param Database database - The Database object the Kohana_Migration is using.
		 * @return bool, true if all hooks return true, false if any return false.
		*/
		public function after ( $migration, $database ) {
			foreach( $this->_after as $after ) {
				if( ! $after( $this, $migration, $database ) ) {
					return false;
				}
			}
			return true;
		}

	}

