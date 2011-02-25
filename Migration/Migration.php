<?php

	class Migration {
		public static function CreateTable ( $name, $args = null ) {
			return new Migration_Statement_CreateTable( $name, $args );
		}
	}