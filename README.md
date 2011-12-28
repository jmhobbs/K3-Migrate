# K3-Migrate Module

A Ko3 Module by [**John Hobbs**](http://twitter.com/jmhobbs)

## Introduction

This module provides a DSL and tools for database migrations for Kohana 3.1.x.

It is loosely inspired by Ruby's [ActiveRecord::Migration](http://api.rubyonrails.org/classes/ActiveRecord/Migration.html)

## Caveats

PHP is not a nice language for DSL's, so there are some dirty hacks and it's a bit verbose. Get over it or pick a new language.

Also, be aware that it is totally possible to create SQL statements that just don't work.  K3-Migrate is not that bright, it will try to do stupid things if you tell it to (especially with indexes).

## Installation

1. Drop the source in your <tt>MODPATH</tt> folder.
2. Add the module to <tt>Kohana::modules</tt> in your bootstrap.php
3. Create the directory <tt>APPPATH/migrations/</tt>

## Examples

There are examples in the <tt>examples</tt> directory.

In K3-Migrate there is usually more than one way to do something, but there are four core statements, outlined below.

Each statement is nested inside of a Migration object's <tt>up</tt> or <tt>down</tt> methods.

## Contributors

 * [@jmhobbs](https://github.com/jmhobbs)
 * [@thomfort](https://github.com/thomfort)
 * [@iGore](https://github.com/iGore)
 * [@Bishop](https://github.com/Bishop)