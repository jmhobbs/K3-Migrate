Implementing Migrations
=======================

You can do three distinct things in a migration:

- Create Tables
- Alter Tables
- Drop Tables

Create Tables
-------------

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

Alter Tables
------------

Altering a table is a lot like creating a table.

TODO


Drop Tables
-----------

The opposite of creating a table is dropping one.  Not much to this command:

    $t = &self::DropTable( 'posts' );
