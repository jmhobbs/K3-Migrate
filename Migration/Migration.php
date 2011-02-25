<?php

	abstract class Migration {

		public abstract function up ();
		public abstract function down ();

		public static function CreateTable ( $name, $args = null ) {
			return new Migration_Statement_CreateTable( $name, $args );
		}
	}