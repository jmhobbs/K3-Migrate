<?php defined('SYSPATH') or die('No direct script access.');

	class AuthORM extends Migration {

		public function up () {

			/*
			CREATE TABLE IF NOT EXISTS `roles` (
			  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
			  `name` varchar(32) NOT NULL,
			  `description` varchar(255) NOT NULL,
			  PRIMARY KEY  (`id`),
			  UNIQUE KEY `uniq_name` (`name`)
			) ENGINE=InnoDB  DEFAULT CHARSET=utf8;
			*/
			$t = &self::CreateTable( 'roles', array( 'created' => false, 'modified' => false ) );
			$t->addColumn( 'string', 'name', array( 'size' => 32, 'null' => false ) );
			$t->addColumn( 'string', 'description', array( 'null' => false ) );
			$t->addKey( array( 'name' => array( 'unique' ) ), array( 'unique', 'name' => 'uniq_name' ) );

			/*
			CREATE TABLE IF NOT EXISTS `roles_users` (
			  `user_id` int(10) UNSIGNED NOT NULL,
			  `role_id` int(10) UNSIGNED NOT NULL,
			  PRIMARY KEY  (`user_id`,`role_id`),
			  KEY `fk_role_id` (`role_id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8;
			*/
			$t = &self::CreateTable( 'roles_users', array( 'id' => false, 'created' => false, 'modified' => false, 'primary_key' => array( 'user_id', 'role_id' ) ) );
			$t->integer = array( 'user_id', 'size' => 10, 'unsigned' => true, 'null' => false );
			$t->integer = array( 'role_id', 'size' => 10, 'unsigned' => true, 'null' => false );
			$t->key = array( 'role_id', array( 'name' => 'fk_role_id' ) );

			/*
			CREATE TABLE IF NOT EXISTS `users` (
			  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
			  `email` varchar(127) NOT NULL,
			  `username` varchar(32) NOT NULL DEFAULT '',
			  `password` varchar(64) NOT NULL,
			  `logins` int(10) UNSIGNED NOT NULL DEFAULT '0',
			  `last_login` int(10) UNSIGNED,
			  PRIMARY KEY  (`id`),
			  UNIQUE KEY `uniq_username` (`username`),
			  UNIQUE KEY `uniq_email` (`email`)
			) ENGINE=InnoDB  DEFAULT CHARSET=utf8;
			*/
			$t = &self::CreateTable( 'users' );
			$t->addColumn( 'string', 'email', array( 'size' => 127 ) );
			$t->addColumn( 'string', 'username', array( 'size' => 32, 'default' => '' ) );
			$t->addColumn( 'string', 'password', array( 'size' => 64 ) );
			$t->addColumn( 'integer', 'logins', array( 'unsigned' => true, 'default' => '0' ) );
			$t->addColumn( 'timestamp', 'last_login' );
			$t->addKey( array( 'username' => array() ), array( 'name' => 'uniq_username', 'type' => 'unique' ) );
			$t->addKey( array( 'email' => array() ), array( 'name' => 'uniq_email', 'type' => 'unique' ) );

			/*
			CREATE TABLE IF NOT EXISTS `user_tokens` (
			  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
			  `user_id` int(11) UNSIGNED NOT NULL,
			  `user_agent` varchar(40) NOT NULL,
			  `token` varchar(40) NOT NULL,
			  `type` varchar(100) NOT NULL,
			  `created` int(10) UNSIGNED NOT NULL,
			  `expires` int(10) UNSIGNED NOT NULL,
			  PRIMARY KEY  (`id`),
			  UNIQUE KEY `uniq_token` (`token`),
			  KEY `fk_user_id` (`user_id`)
			) ENGINE=InnoDB  DEFAULT CHARSET=utf8;
			*/
			$t = &self::CreateTable( 'user_tokens' );
			$t->addColumn( 'integer', 'user_id', array( 'unsigned' => true, 'size' => 11 ) );
			$t->addColumn( 'string', 'user_agent', array( 'size' => 40 ) );
			$t->addColumn( 'string', 'token', array( 'size' => 40 ) );
			$t->addColumn( 'string', 'type', array( 'size' => 100 ) );
			$t->addColumn( 'timestamp', 'created');
			$t->addColumn( 'timestamp', 'expires');
			$t->addKey( array( 'token' => array() ), array( 'name' => 'uniq_token', 'type' => 'unique') );
			$t->addKey( array( 'user_id' => array() ), array( 'name' => 'fk_user_id' ) );

			/*
			// Example of a post-query hook that can abort the migration.
			$t->runAfter( function ( $stmnt, $migration, $db ) { 
				print "# If you return false in a hook it \n";
				print "# rolls back and cancels the migration.\n\n";
				return false;
			} );
			*/

			/*
			ALTER TABLE `roles_users`
			  ADD CONSTRAINT `roles_users_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
			  ADD CONSTRAINT `roles_users_ibfk_2` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE;
			*/
			$t = &self::AlterTable( 'roles_users' );
			$t->addForeignKey( array( 'user_id' ), 'users', array( 'id' ), array( 'delete' => 'cascade', 'name' => 'roles_users_ibfk_1' ) );
			$t->addForeignKey( array( 'role_id' ), 'roles', array( 'id' ), array( 'delete' => 'cascade', 'name' => 'roles_users_ibfk_2' ) );

			/*
			ALTER TABLE `user_tokens`
			  ADD CONSTRAINT `user_tokens_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
			*/
			$t = &self::AlterTable( 'user_tokens' );
			$t->addForeignKey( array( 'user_id' ), 'users', array( 'id' ), array( 'delete' => 'cascade', 'name' => 'user_tokens_ibfk_1' ) );
		}
		public function down () {
			/* DROP TABLE `user_tokens`; */
			$t = &self::DropTable( 'user_tokens' );

			/* DROP TABLE `roles_users`; */
			$t = &self::DropTable( 'roles_users' );

			/* DROP TABLE `users`; */
			$t = &self::DropTable( 'users' );

			/* DROP TABLE `roles`; */
			$t = &self::DropTable( 'roles' );
		}

	}

