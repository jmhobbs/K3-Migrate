K3-Migrate Module
=================

A Ko3 Module by [**John Hobbs**](http://twitter.com/jmhobbs) of
**[Little Filament, Inc.](http://littlefilament.com)**

Introduction
------------

This module provides a DSL and tools for database migrations for Kohana 3.1.x.

It is inspired by Ruby's [ActiveRecord::Migration](http://api.rubyonrails.org/classes/ActiveRecord/Migration.html)

Caveats
-------

PHP is not a nice language for DSL's, so there are some dirty hacks and it's a bit verbose. Get over it or pick a new language.

Also, be aware that it is totally possible to create SQL statements that just don't work.  Migrate is not that bright, it will try to do stupid things if you tell it to (especially with indexes).

Installation
------------

1. Drop the source in your <tt>MODPATH</tt> folder.
2. Add the module to <tt>Kohana::modules</tt> in your bootstrap.php
3. Create the directory <tt>APPPATH/migrations/</tt>

