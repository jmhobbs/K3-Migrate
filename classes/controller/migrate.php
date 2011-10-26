<?php defined('SYSPATH') or die('No direct access allowed.');

	class Controller_Migrate extends Controller {

		public function before () {
			parent::before();
			if( ! Kohana::$is_cli ) { throw new Kohana_Exception( "CLI Access Only" ); }
			$this->runner = new Migration_Manager(Kohana::$config->load('migration'));
			print "\n=============[ K3-Migrate ]============\n\n";
		}

		public function after () {
			parent::after();
			print "\n=======================================\n\n";
		}

		public function action_index () {
			$current_version = $this->runner->getSchemaVersion();
			if( empty( $current_version ) ) {
				print " You have not performed any migrations yet!\n\n";
			}

			print " Total Migrations: " . count( $this->runner->enumerateMigrations() ) . "\n\n";

			foreach( $this->runner->enumerateMigrations() as $migration ) {
				if( $this->runner->migrationNameToVersion( $migration ) == $current_version ) {
					print " You Are Here =>  ";
				}
				else {
					print "                  ";
				}
				print "$migration\n";
			}
		}

		public function action_up () {
                        $target = $this->request->param('id');
                        
			foreach( $this->runner->enumerateMigrations() as $migration ) {
				print "==[ $migration ]==\n";
				$this->runner->runMigrationUp( $migration );
				print "\n";
			}
		}

		public function action_down () {
                        $target = $this->request->param('id');
                        
			foreach( array_reverse($this->runner->enumerateMigrations()) as $migration ) {
				print "==[ $migration ]==\n";
				$this->runner->runMigrationDown( $migration );
				print "\n";
			}
		}

		public function action_print () {
                        $target = $this->request->param('id');
                    
			foreach( $this->runner->enumerateMigrations() as $migration ) {
				print "======[ $migration ]======\n";
				print "===[ UP ]===\n";
				print $this->runner->getMigrationUp( $migration );
				print "\n";
				print "==[ DOWN ]==\n";
				print $this->runner->getMigrationDown( $migration );
				print "\n";
			}
		}

		public function action_seed () {
			$this->runner->seed();
		}

		public function action_create () {         
                        $class_name = $this->request->param('id');
                    
			if( null == $class_name ) {
				throw new Kohana_Exception( "A Class Name Is Required" );
			}
			$file_name = $this->runner->create( $class_name );
			print "Created migration `$class_name` in file `" . $file_name . "`\n";
		}
	}

