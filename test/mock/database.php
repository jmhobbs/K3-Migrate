<?php

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

