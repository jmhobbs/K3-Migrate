<?php

	/*!
		Mock object to emulate key parts of http://kohanaframework.org/3.1/guide/api/Database
		for testing and examples.
	*/
	class Mock_Database {

		const INSERT = 1;

		public function begin () {
			print "BEGIN TRANSACTION;\n";
		}

		public function commit () {
			print "COMMIT;\n";
		}

		public function rollback () {
			print "ROLLBACK;\n";
		}

		public function query ( $type, $query ) {
			print "$query\n";
		}

	}

