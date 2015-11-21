<?php
	/** @var $core Core */
	require('../lib/bootstrap.php');

	if ($core->config()->getConfig('cookies_enabled')) {
		session_start();
	}

	session_set_cookie_params($core->config()->cookiesValidFor());


	if ($core->dev()->isDeveloperMode()) {
		error_reporting(E_ALL & ~E_STRICT);
	} else {
		error_reporting(0);
	}

	/** Autloader */
	$core->modules()->bootstrap();

	/** Route handler */
	$core->loader()->route();

	/** If there is session error, do something, if action is set */
	$core->loader()->executeErrorAction();

	if ($core->isCLI()) {
		exit;
	} else {
		$core->templates()->printLayout();
	}

