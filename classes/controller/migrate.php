<?php defined('SYSPATH') or die('No direct access allowed.');

	class Controller_Migrate extends Controller {

		public function before () {
			parent::before();
			if( ! Kohana::$is_cli ) { throw new Kohana_Exception( "CLI Access Only" ); }
			$this->runner = new Migration_Manager(Kohana::config('migration'));
		}

		public function action_index () {
			print "You have " . count( $this->runner->enumerateMigrations() ) . " total migrations.\n";
		}

		public function action_up () {
			foreach( $this->runner->enumerateMigrations() as $migration ) {
				print "==[ $migration ]==\n";
				$this->runner->runMigrationUp( $migration );
				print "\n";
			}
		}

		public function action_down () {
			foreach( $this->runner->enumerateMigrations() as $migration ) {
				print "==[ $migration ]==\n";
				$this->runner->runMigrationDown( $migration );
				print "\n";
			}
		}

		public function action_seed () {
			$this->runner->seed();
		}

		public function action_create ( $class_name = null ) {
			if( null == $class_name ) {
				throw new Kohana_Exception( "A Class Name Is Required" );
			}
			$file_name = $this->runner->create( $class_name );
			print "Created migration `$class_name` in file `" . $file_name . "`\n";
		}
	}
