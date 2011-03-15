Creating Migrations
===================

The standard location for migration files is in <tt>APPPATH/migrations/</tt> You can change this by copying and editing the config/migration.php file.

To create a migration you can run the generator from the command line, in the Kohana root:

    ~/kohana-3.1-master-5$ php index.php --uri=migrate/create/PostModel
    Created migration `PostModel` in file `1300074585_post_model.php`
    ~/kohana-3.1-master-5$

In this example PostModel is our migration name.  The name you use doesn't matter, but it should be camel-cased with no spaces.  Pick something that will remind you what the migration does.

This will put create a migration file at <tt>APPPATH/migrations/1300074585_post_model.php</tt>  At this point the file will be just an empty shell:

    <?php defined('SYSPATH') or die('No direct script access.');

    	class PostModel extends Migration {
    		public function up () {}
    		public function down () {}
    	}

You will now need to [implement the migration](implementing_migrations) before it will be of any use.
