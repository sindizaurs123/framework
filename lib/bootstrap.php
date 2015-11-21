<?php

	$loadOrder = array(
		'../modules/external/kint/Kint.class.php',
		'../modules/external/spyc.php',
		'Core.class.php',
		'Log.class.php',
		'Developer.class.php',
		'Config.class.php',
		'ORM.class.php',
		'Modules.class.php',
		'Loader.class.php',
		'Templates.class.php',
		'CMS.class.php',
		'Language.class.php',
		'Object.class.php',
		'CLI.class.php',
		'Date.class.php',


		'Functions.php',
	);

	/*
	 * Include these and unset array.
	 */

	foreach ($loadOrder as $num => $file)
		{
			$num++;
			require($file);
		}

	unset($loadOrder);

	$core = new Core;
	$config = new Config;

	$db = (object)$config->loadYAML('../config/database.yaml');

	/*
	 * Idiorm ORM class.
	 * http://idiorm.readthedocs.org/en/latest/
	 * https://github.com/j4mie/idiorm
	 */

	ORM::configure(array(
		'connection_string' => "$db->driver:host=$db->hostname;dbname=$db->database",
		'username' => $db->username,
		'password' => $db->password,
	));
	ORM::configure('driver_options', array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8'));

	/*
	 * ...keep loading my stuff.
	 */

	$log = new Log();
	$config = new Config;
	$modules = new Modules;
	$cms = new CMS;
	$loader = new Loader;
	$templates = new Templates;
	$language = new Language;
	$dev = new Developer;
	$cli = new CLI;

	/*
	 * I should move these two to bootstrap module, for easy overriding.
	 */




