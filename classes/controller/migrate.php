<?php defined('SYSPATH') or die('No direct access allowed.');

class Controller_Migrate extends Controller {
	/**
	 * @var Migration_Manager
	 */
	protected $runner;

	public function before()
	{
		parent::before();
		if ( ! Kohana::$is_cli)
		{
			throw new Kohana_Exception("CLI Access Only");
		}
		$this->runner = new Migration_Manager(Kohana::$config->load('migration'));

		print "\n=============[ K3-Migrate ]============\n\n";
	}

	public function after()
	{
		parent::after();
		print "\n=======================================\n\n";
	}

	public function action_index()
	{
		$applied_versions = $this->runner->getAppliedVersions();
		$current_version = $this->runner->getSchemaVersion();
		if (empty($current_version))
		{
			print " You have not performed any migrations yet!\n\n";
		}

		$migrations = $this->runner->enumerateMigrations();
		print " Total Migrations: ".count($migrations)."\n\n";

		foreach ($migrations as $migration)
		{
			$version = $this->runner->migrationNameToVersion($migration);
			printf("  (%s)  %s  %s\n",
				in_array($version, $applied_versions) ? '*' : ' ',
				$version == $current_version ? '=>' : '  ',
				$migration
			);
		}

		$orphans_migrations = $this->runner->getOrphansMigrations();
		if (count($orphans_migrations))
		{
			print "\nThere are applied migrations, that don't exist any more:\n\n";

			foreach ($orphans_migrations as $orphans_migration)
			{
				printf("   !       %s\n", $orphans_migration);
			}
		}
	}

	public function action_up()
	{
		$target = $this->request->param('id');

		$performed = 0;
		foreach ($this->runner->enumerateUpMigrations() as $migration)
		{
			print "==[ $migration ]==\n";
			$this->runner->runMigrationUp($migration);
			print "\n";

			if ($target > 0 && $target == ++$performed)
				break;
		}
	}

	public function action_down()
	{
		$target = $this->request->param('id');

		if ($target)
		{
			$performed = 0;
			foreach ($this->runner->enumerateDownMigrations() as $migration)
			{
				print "==[ $migration ]==\n";
				$this->runner->runMigrationDown($migration);
				print "\n";

				if ($target > 0 && $target == ++$performed)
					break;
			}
		}
		else
		{
			print "You should to specify step.\n";
		}
	}

	/**
	 * Mark all migrations as performed
	 */
	public function action_fake()
	{
		$migrations = $this->runner->enumerateUpMigrations();

		$migration = array_pop($migrations);

		print "==[ $migration ]==\n";

		$this->runner->setSchemaVersion(
			$this->runner->migrationNameToVersion($migration)
		);
	}

	public function action_print()
	{
		$target = $this->request->param('id');

		$performed = 0;
		foreach ($this->runner->enumerateMigrationsReverse() as $migration)
		{
			print "======[ $migration ]======\n\n";
			print "===[ UP ]===\n";
			print $this->runner->getMigrationClass($migration)->queryUp();
			print "\n";
			print "==[ DOWN ]==\n";
			print $this->runner->getMigrationClass($migration)->queryDown();
			print "\n";

			if ($target > 0 && $target == ++$performed) break;
		}
	}

	public function action_seed()
	{
		$this->runner->seed();
	}

	public function action_create()
	{
		$class_name = $this->request->param('id');

		if (null == $class_name)
		{
			throw new Kohana_Exception("A Class Name Is Required");
		}
		$file_name = $this->runner->create($class_name);
		print "Created migration `$class_name` in file `".$file_name."`\n";
	}
}