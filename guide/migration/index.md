K3-Migration Module
==============

A Ko3 Module by [**John Hobbs**](http://twitter.com/jmhobbs) of
**[Little Filament, Inc.](http://littlefilament.com)**

Introduction
------------

This module provides a PHP DSL and tools for database migrations for Kohana 3.1.x.

It is inspired by Ruby's [ActiveRecord::Migration](http://api.rubyonrails.org/classes/ActiveRecord/Migration.html)

Caveats
-------

PHP is not a nice language for DSL's, so there are some dirty hacks and it's a bit verbose. Get over it or pick a new language.

Installation
------------

K3-Migration is a simple, standard module.

1. Drop the source in your MODPATH folder.
2. Add the module to Kohana::modules in your bootstrap.php

Usage
-----

The standard location for migrations is in APPPATH/migrations/  You can change this by copying and editing the config/migration.php file.

### Creating Migrations ###

To create a migration you can run the generator from the command line, in the Kohana root:

    ~/kohana-3.1-master-5$ php index.php --uri=migration/create/PostModel
    Created migration `PostModel` in file `1300074585_post_model.php`
    ~/kohana-3.1-master-5$

In this example PostModel is our migration name.  The name you use doesn't matter, but it should be camel-cased with no spaces.  Pick something that will remind you what the migration does.

This will put create a migration file at APPPATH/migrations/1300074585_post_model.php  At this point the file will be just a shell:

    <?php defined('SYSPATH') or die('No direct script access.');

    	class PostModel extends Migration {
    		public function up () {}
    		public function down () {}
    	}

Now we need to implement the migration.


### Implementing Migrations ###

You can do three distinct things in a migration:

- Create A Table
- Drop A Table
- Alter A Table

#### Create A Table ####

The first step to creating a table is to get a reference to it.

    $t = &self::CreateTable( 'posts' );

Each table has a integer primary key called 'id' by default, but you can alter that with options on CreateTable.

Now you can add columns.  The format for adding columns is:

    $t->addColumn( [TYPE], [NAME], [OPTIONS] );

So, for example, you can add a string column named "title", like so:

    $t->addColumn( 'string', 'title' );

or if you have some options you want, you can specify those:

    $t->addColumn( 'string', 'title', array( 'default' => 'My Post Title' ) );

The available column types are:

- String
- Text
- Integer
- Blob
- DateTime
- Timestamp
- Decimal
- Float

There is also a short form of add column, which are masquerading as property assignments:

    $t->string = 'title';

to pass options, set an array instead:

    $t->string = array( 'title', array( 'default' => 'My Post Title' ) );

You can also add indexes to the table.  This is a bit more complex.

    $t->addIndex( Array( [COLUMN] => [OPTIONS] ), [OPTIONS] );

Here is a single column, unique index with no special options.

    $t->addIndex( array( 'slug' => array() ), array( 'unique' ) );

Here is a two column, full text index with a length option on the slug column:

    $t->addIndex( array(
	        'slug' => array( 'length' => 20 ),
	        'title' => array()
	    ),
	    array( 'fulltext' )
	);

#### Drop A Table ####

The opposite of creating a table is dropping one.  Not much to this command:

    $t = &self::DropTable( 'posts' );

#### Alter A Table ####

Altering a table is a lot like creating a table.

TODO

### Seed Data ###

This module also will recognize and run a seed file, which is a PHP script that runs under a Kohana request and can seed your database.

This is perfect for quickly inserting test data or core users.  Anything in APPPATH/migrations/seed.php will be executed when you run the command line:

    ~/kohana-3.1-master-5$ php index.php --uri=migration/seed
