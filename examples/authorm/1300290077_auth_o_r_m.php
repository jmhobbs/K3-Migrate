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
			// TODO Unique Key on name called uniq_name
			$t = &self::CreateTable( 'roles ');
			$t->addColumn( 'string', 'name', array( 'size' => 32 ) );
			$t->addColumn( 'string', 'description' );



			/*
			CREATE TABLE IF NOT EXISTS `roles_users` (
			  `user_id` int(10) UNSIGNED NOT NULL,
			  `role_id` int(10) UNSIGNED NOT NULL,
			  PRIMARY KEY  (`user_id`,`role_id`),
			  KEY `fk_role_id` (`role_id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8;
			*/
			// TODO: No PK!
			$t = &self::CreateTable( 'roles_users' )
			$t->integer = 'user_id';
			$t->integer = 'role_id';

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
			// TODO: Keys
			$t = &self::CreateTable( 'users' );
			$t->addColumn( 'string', 'email', array( 'size' => 127 ) );
			$t->addColumn( 'string', 'username', array( 'size' => 32, 'default' => '' ) );
			$t->addColumn( 'string', 'password', array( 'size' => 64 ) );
			$t->addColumn( 'integer', 'logins', array( 'unsigned' => true, 'default' => '0' ) );
			$t->addColumn( 'timestamp', 'last_login' );

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
			// TODO: Keys
			$t = &self::CreateTable( 'user_tokens' );
			$t->addColumn( 'integer', 'user_id', array( 'unsigned' => true, 'size' => 11 ) );
			$t->addColumn( 'string', 'user_agent', array( 'size' => 40 ) );
			$t->addColumn( 'string', 'token', array( 'size' => 40 ) );
			$t->addColumn( 'string', 'type', array( 'size' => 100 ) );
			$t->addColumn( 'timestamp', 'created');
			$t->addColumn( 'timestamp', 'expires');

			/*
			ALTER TABLE `roles_users`
			  ADD CONSTRAINT `roles_users_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
			  ADD CONSTRAINT `roles_users_ibfk_2` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE;
			*/
			// TODO

			/*
			ALTER TABLE `user_tokens`
			  ADD CONSTRAINT `user_tokens_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
			*/
			// TODO
		}
		public function down () {
			// TODO
		}
	}

