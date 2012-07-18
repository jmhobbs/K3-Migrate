<?php

class Kohana_Migration_Manager {
	/**
	 * @var Config
	 */
	protected $config = null;
	protected $database = null;

	protected $appliedMigrations = array();
	protected $existsMigrations = array();

	public function __construct($config)
	{
		if ( ! is_dir($config->path))
		{
			throw new Kohana_Exception("Invalid Migrations Path: {$config->path}");
		}

		$this->config = $config;

		if (($database = getenv('ENVIRONMENT')) !== false)
		{
			$this->database = $database;
		}
		else
		{
			$this->database = $this->config->database;
		}

        $this->loadAppliedMigrations();
        $this->loadExistsMigrations();
    }

	public function enumerateMigrations()
	{
		return $this->existsMigrations;
	}

	public function enumerateMigrationsReverse()
	{
		return array_reverse($this->enumerateMigrations());
	}

	public function enumerateUpMigrations()
	{
		$current = $this->getSchemaVersion();

		return array_filter(
			$this->enumerateMigrations(),
			function ($file) use ($current)
			{
				return Migration_Manager::migrationNameToVersion($file) > $current;
			}
		);
	}

	public function enumerateDownMigrations()
	{
		$current = $this->getSchemaVersion();

		return array_reverse(
			array_filter(
				$this->enumerateMigrations(),
				function ($file) use ($current)
				{
					return Migration_Manager::migrationNameToVersion($file) <= $current;
				}
			)
		);
	}

	public function getAppliedVersions()
	{
		return $this->appliedMigrations;
	}

	protected function loadAppliedMigrations()
	{
		$version_file = $this->getSchemaVersionFileName();

		if (file_exists($version_file))
		{
			$this->appliedMigrations = file($version_file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
		}
	}

	protected function loadExistsMigrations()
	{
		$files = scandir($this->config->path);

		$this->existsMigrations = array_map(
			'Migration_Manager::fileNameToMigrationName',
			array_filter(
				$files,
				'Migration_Manager::isMigrationFile'
			)
		);
	}

	public function getSchemaVersion()
	{
		$versions = $this->getAppliedVersions();

		return count($versions) ? end($versions) : 0;
	}

	public function setSchemaVersion($version, $deleted = false)
	{
		$version_file = $this->getSchemaVersionFileName();

		if ($deleted)
		{
			$this->appliedMigrations = array_diff($this->appliedMigrations, array($version));
		}
		else
		{
			$this->appliedMigrations[] = $version;
		}
		sort($this->appliedMigrations);

		file_put_contents($version_file, implode(PHP_EOL, $this->appliedMigrations));
	}

	protected function getSchemaVersionFileName()
	{
		if ( ! is_dir($this->config->path))
		{
			mkdir($this->config->path);
		}

		return rtrim($this->config->path, DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR.'.version-'.$this->database;
	}

	public function lastSchemaVersion()
	{
		$migrations = $this->enumerateMigrations();
		return self::migrationNameToVersion(end($migrations));
	}

	public function getOrphansMigrations()
	{
		return array_diff(
			$this->getAppliedVersions(),
			array_map(
				'Migration_Manager::migrationNameToVersion',
				$this->enumerateMigrations()
			)
		);
	}

	/**
	 * @param string $name
	 * @return Migration
	 */
	public function getMigrationClass($name)
	{
		require_once($this->config->path.DIRECTORY_SEPARATOR.$name.'.php');

		$class_name = self::migrationNameToClassName($name);
		$class = new $class_name();
		return $class;
	}

	public function runMigrationUp($name)
	{
		$this->getMigrationClass($name)->migrateUp(Database::instance($this->database));
		$this->setSchemaVersion(self::migrationNameToVersion($name));
	}

	public function runMigrationDown($name)
	{
		$this->getMigrationClass($name)->migrateDown(Database::instance($this->database));
		$this->setSchemaVersion(self::migrationNameToVersion($name), true);
	}

	public function seed()
	{
		$seed_path = $this->config->path.DIRECTORY_SEPARATOR.'seed.php';
		if (is_file($seed_path))
		{
			require_once($seed_path);
		}
		else
		{
			throw new Exception('Seed file does not exist: '.$seed_path);
		}
	}

	public function create($class_name)
	{
		$name = time().'_'.self::classNameToMigrationName($class_name);
		$class = <<<END
<?php defined('SYSPATH') or die('No direct script access.');

class $class_name extends Migration {
	public function up()
	{

	}

	public function down()
	{

	}
}
END;
		file_put_contents($this->config->path.DIRECTORY_SEPARATOR.$name.'.php', $class);

		return $name.'.php';
	}

	/**
	 * Check if the given filename matches the migration file format:
	 * [timestamp]_[migration_name].php
	 * @param string $filename
	 * @return bool
	 */
	public static function isMigrationFile($filename)
	{
		return (0 != preg_match('/[0-9]+_[a-zA-Z0-9_]+\.php/', basename($filename)));
	}

	/**
	 * Convert a file name into a migration name (i.e. strip the extension)
	 * Example: 1299086729_user_migration.php => 1299086729_user_migration
	 * @param string $filename
	 * @return string
	 */
	public static function fileNameToMigrationName($filename)
	{
		$position = strrpos(strtolower(basename($filename)), '.php');
		if ($position !== false)
		{
			return substr(basename($filename), 0, $position);
		}
	}

	/**
	 * Convert a migration name into the corresponding class name.
	 * Example: 1299086729_user_migration => UserMigration
	 * @param string $migration_name
	 * @return string
	 */
	public static function migrationNameToClassName($migration_name)
	{
		return str_replace(
			' ', '',
			ucwords(
				str_replace(
					'_', ' ',
					preg_replace('/^[0-9]+_/', '', $migration_name)
				)
			)
		);
	}

	/**
	 * Convert a class name to migration name.
	 * Example: UserMigration => user_migration
	 * @param string $class_name
	 * @return string
	 */
	public static function classNameToMigrationName($class_name)
	{
		preg_match_all('/[A-Z][^A-Z]*/', $class_name, $results);
		$name = strtolower(implode('_', $results[0]));
		if (strlen($name) == 0)
		{
			throw new Exception('Invalid class name: '.$class_name);
		}
		return $name;
	}

	/**
	 * Convert a migration (file) name into it's version.
	 * Example: 1299086729_user_migration => 1299086729
	 * @param string $migration_name
	 * @return int
	 */
	public static function migrationNameToVersion($migration_name)
	{
		$split = explode('_', $migration_name);
		return intval($split[0]);
	}
}

