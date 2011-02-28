<?php

        class Migration_Statement_ChangeTable extends Migration_Statement {

                protected $_tableName;
                protected $_engine;
                protected $_charset;

		public function __construct( $tableName ) {
			$this->_tableName = $tableName;
		}

	}
