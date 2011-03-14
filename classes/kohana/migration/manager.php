<?php

	class Kohana_Migration_Manager {

		protected $config = null;

		public function __construct ( $config ) {
			if( ! is_dir( $config->path ) ) { throw new Kohana_Exception( "Invalid Migrations Path: {$config->path}" ); }
			$this->config = $config;
		}

		public function enumerateMigrations () {
			$files = scandir( $this->config->path );
			return array_map(
				'Migration_Manager::fileNameToMigrationName',
				array_filter(
					$files,
					'Migration_Manager::isMigrationFile'
				)
			);
		}

		public function runMigrationUp ( $name ) {
			require_once( $this->config->path . '/' . $name . '.php');
			$classname = self::migrationNameToClassName( $name );
			$class = new $classname();
			$this->executeSQL( $class->queryUp() );
		}

		public function runMigrationDown ( $name ) {
			require_once( $this->config->path . '/' . $name . '.php');
			$classname = self::migrationNameToClassName( $name );
			$class = new $classname();
			$this->executeSQL( $class->queryDown() );
		}

		public function executeSQL( $sql ) {
			$queries = explode( ';', $sql );
			$db = Database::instance(); // TODO: Named DBs?
			foreach( $queries as $query ) {
				$query = trim( $query );
				if( empty( $query ) ) { continue; }
				$db->query( Database::UPDATE, $query, false );
			}
		}

		public function seed () {
			if( is_file( $this->config->path . '/seed.php' ) ) {
				require_once( $this->config->path . '/seed.php');
			}
		}

		public function create ( $class_name ) {
			$name = time() . '_' . self::classNameToMigrationName( $class_name );
			$class = <<<END
<?php defined('SYSPATH') or die('No direct script access.');

	class $class_name extends Migration {
		public function up () {}
		public function down () {}
	}


END;
			file_put_contents( $this->config->path . '/' . $name . '.php', $class );
			return $name . '.php';
		}

		/**
		* Check if the given filename matches the migration file format:
		* [timestamp]_[migration_name].php
		*/
		public static function isMigrationFile ( $filename ) {
			return ( 0 != preg_match( '/[0-9]+_[a-zA-Z0-9_]+\.php/', basename( $filename ) ) );
		}

		/**
		* Convert a file name into a migration name (i.e. strip the extension)
		*
		* Example: 1299086729_user_migration.php => 1299086729_user_migration
		*/
		public static function fileNameToMigrationName ( $filename ) {
			$position = strrpos( strtolower( basename( $filename ) ), '.php' );
			if( false !== $position ) {
				return substr( basename( $filename ), 0, $position );
			}
		}

		/**
		* Convert a migration name into the corresponding class name.
		*
		* Example: 1299086729_user_migration => UserMigration
		*/
		public static function migrationNameToClassName ( $migration_name ) {
			return str_replace(
				' ', '',
				ucwords(
					str_replace(
						'_', ' ',
						preg_replace( '/^[0-9]+_/', '', $migration_name )
					)
				)
			);
		}

		/**
		* Convert a class name to migration name.
		*
		* Example: UserMigration => user_migration
		*/
		public static function classNameToMigrationName ( $class_name ) {
			preg_match_all( '/[A-Z][^A-Z]*/', $class_name, $results);
			return strtolower( implode( '_', $results[0] ) );
		}
	}
