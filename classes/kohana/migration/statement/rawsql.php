<?php

	class Kohana_Migration_Statement_RawSql extends Kohana_Migration_Statement {

		protected $_sql;

		public function __construct ( $sql ) {
			$this->_sql = $sql;
		}

		public function toSQL () {
			return $this->_sql;
		}

	}
