<?php
	// Auth ORM Default Roles
	$admin_role = ORM::factory('role');
	$admin_role->name = 'admin';
	$admin_role->description = 'Login privileges, granted after account confirmation';
	$admin_role->save();

	$admin_role = ORM::factory('role');
	$admin_role->name = 'admin';
	$admin_role->description = 'Administrative user, has access to everything.';
	$admin_role->save();

