Seed Data
=========

This module also will recognize and run a seed file, which is a PHP script that runs under a Kohana request and can seed your database.

This is perfect for quickly inserting test data or core users.  Anything in <tt>APPPATH/migrations/seed.php</tt> will be executed when you run the command line:

    ~/kohana-3.1-master-5$ php index.php --uri=migration/seed
