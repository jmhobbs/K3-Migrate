<?php

	class Migration_Manager {

		protected $migrations_path = null;

		public function __construct ( $migrations_path ) {
			$this->migrations_path = $migrations_path;
		}

		public function enumerateMigrations () {
			$files = scandir( $this->migrations_path );
			return array_map(
				'Migration_Manager::fileNameToMigrationName',
				array_filter(
					$files,
					'Migration_Manager::isMigrationFile'
				)
			);
		}

		public function runMigrationUp ( $name ) {
			require_once( $this->migrations_path . '/' . $name . '.php');
			$classname = self::migrationNameToClassName( $name );
			$class = new $classname();
			return $class->queryUp();
		}

		public function runMigrationDown ( $name ) {
			require_once( $this->migrations_path . '/' . $name . '.php');
			$classname = self::migrationNameToClassName( $name );
			$class = new $classname();
			return $class->queryDown();
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

	}
