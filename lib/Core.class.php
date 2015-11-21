<?php

	class Core
	{

		public function __construct()
		{
			$this->currentGlobal = false;
			$this->globalModules = array();
			$this->var = array();
		}

		/** !! INTERNAL USE ONLY !!
		 * Sets internal global class, such as Modules, Loader, Template...
		 *
		 * @param object $global Global class.
		 *
		 * @return self
		 */
		public function setGlobal($global)
		{
			$this->currentGlobal = $global;

			return $this;
		}

		/**
		 * Returns global class if found, false otherwise.
		 * @return mixed $global
		 */
		private function getGlobal($global)
		{
			if (!$global) {
				$global = $this->currentGlobal;
			}

			global $$global;

			if (is_object($$global)) {
				return $$global;
			} else {
				return false;
			}
		}

		/** @return CMS */
		public function cms()
		{
			return $this->getGlobal('cms');
		}

		/** @return Log */
		public function log()
		{
			return $this->getGlobal('log');
		}

		/** @return Modules */
		public function modules()
		{
			return $this->getGlobal('modules');
		}


		/** @return Config */
		public function config()
		{
			return $this->getGlobal('config');
		}

		/** @return Loader */
		public function loader()
		{
			return $this->getGlobal('loader');
		}


		/** @return Templates */
		public function templates()
		{
			return $this->getGlobal('templates');
		}

		/** @return Language */
		public function lang()
		{
			return $this->getGlobal('language');
		}

		/** @return Developer */
		public function dev()
		{
			return $this->getGlobal('dev');
		}

		/** @return string */
		public function __($string, $language = false)
		{
			return $this->lang()->__($string, $language);
		}

		/** @return bool */
		public function debug($level = false)
		{
			return $this->dev()->debug($level);
		}

		public function url($id = false)
		{
			/*
			 * url() returns full path, without domain
			 * e.g. path = http://example.com/firstarg/secondarg/thirdarg
			 * url() === '/firstarg/secondarg/thirdarg'

			 * url(int) returns name of int argument
			 * e.g. path = http://example.com/firstarg/secondarg/thirdarg
			 * url(1) === 'firstarg'; url(2) === 'secondarg' and so on.

			 * url(string) checks if argument with that name exists
			 * e.g. path = http://example.com/firstarg/secondarg/thirdarg
			 * url('thirdarg') === true; url('iDoNotExist') === false
			 */

			$start = 0;
			$params = array();

			if (isset($_SERVER['REQUEST_URI'])) {
				$request = $_SERVER['REQUEST_URI'];
			} else {
				$request = '/';
			}

			foreach (explode('/', $request) as $part) {
				$params[$start] = $part;
				$start++;
			}
			if (!$id) {
				return $_SERVER['REQUEST_URI'];
			} elseif (!is_int($id)) {
				if (in_array($id, $params)) {
					return true;
				} else {
					return false;
				}
			} else {
				return $params[$id];
			}
		}

		/**
		 * Returns current domain name.
		 *
		 * @return string;
		 */
		public function domainName()
		{

			if (isset($_SERVER['HTTP_HOST'])) {
				$domain = $_SERVER['HTTP_HOST'];
			} else {
				$domain = false;
			}

			return $domain;
		}

		/**
		 * If no argument is supplied, returns clients' IP address.
		 * Otherwise compares if clients' IP address matches param.
		 *
		 * @param string $ip IP address
		 *
		 * @return mixed Returns string of IP address, if no param is supplied, true/false otherwise.
		 */
		public function ip($ip = false)
		{

			if (!$ip) {
				return $_SERVER['REMOTE_ADDR'];
			} else {
				if ($ip == $this->ip()) {
					return true;
				} else {
					return false;
				}
			}
		}


		/**
		 * Returns time in mySQL format
		 *
		 * @return string Time
		 */
		public function time()
		{
			return date('H:i:s');
		}

		/** Returns date in mySQL format
		 *
		 * @return string Date
		 */
		public function date()
		{
			return date('d-m-Y');
		}

		/** Returns date in mySQL DateTime format
		 *
		 * @return string DateTime
		 */
		public function dateTime()
		{
			return date('d-m-Y H:i:s');
		}


		/**
		 * Redirects user and terminates php execution
		 *
		 * @param mixed $url set to actual url to redirect to it, false to redirect to root.
		 *
		 * @return void
		 */
		public function redirect($url = false)
		{
			if (!$url) {
				header('Location: /');
			} else {
				header('Location: ' . $url);
			}
			exit;
		}


		/**
		 * Checks if php is run from command line.
		 *
		 * @return bool True if run from cli, false otherwise.
		 */
		public function isCLI()
		{
			if (php_sapi_name() === 'cli') {
				return true;
			} else {
				return false;
			}
		}

		/** For setting global system variable to be accessible from everywhere
		 *
		 * @param string $node  Key
		 * @param mixed  $value Value
		 * @param bool   $force Set true to override existing variable. Not recommended.
		 *
		 * @return bool True if variable successfully set, False otherwise
		 */
		public function setVar($node, $value, $force = false)
		{
			if (!$this->checkVar($node) or $force) {
				$this->var[$node] = $value;

				return true;
			} else {
				return false;
			}
		}


		/**
		 * Checks if system variable is already defined.
		 *
		 * @param string $node Key
		 *
		 * @return bool True if var is already defined, false otherwise.
		 */
		public function checkVar($node)
		{
			if (isset($this->var[$node])) {
				return true;
			} else {
				return false;
			}
		}

		/**
		 * Gets global module
		 *
		 * @param string $module Key
		 * @return mixed Returns module if it exists, null otherwise.
		 */
		public function getGlobalModule($module)
		{
			if ($this->checkVar($module)) {
				return $this->var[$module];
			} else {
				return null;
			}
		}

		/** Sets global module. Must be initialized.
		 * I'm not even sure why I'm adding this, because there is literally no way to get autocompletion in IDE
		 * because it's being lodaded dynamically. Also, there's no way for developers to know which module it will return
		 * I'm not recommending to use it, but do it if you can declare what it will return.
		 *
		 * @param string $module  Key
		 * @param object  $object
		 * @param bool   $force Set true to override existing variable. Not recommended.
		 *
		 * @return bool True if variable successfully set, False otherwise
		 */
		public function setGlobalModule($module, $object, $force = false)
		{
			if (!$this->checkVar($module) or $force) {
				$this->globalModules[$module] = $object;

				return true;
			} else {
				return false;
			}
		}


		/**
		 * Checks if global module is already defined.
		 *
		 * @param string $module Key
		 *
		 * @return bool True if module is already defined, false otherwise.
		 */
		public function checkGlobalModule($module)
		{
			if (isset($this->globalModules[$module])) {
				return true;
			} else {
				return false;
			}
		}

		/**
		 * Gets system variable
		 *
		 * @param string $node Key
		 * @return mixed Returns value of var if it exists, null otherwise.
		 */
		public function getVar($node)
		{
			if ($this->checkVar($node)) {
				return $this->globalModules[$node];
			} else {
				return null;
			}
		}

		public function version($module = false, $context = false)
		{
			if (!$module) {
				if ($this->cms()->getVar('app_version')) {
					$version = $this->cms()->getVar('app_version');
				} else {
					$version = $this->config()->getConfig('version');
				}

				if ($this->cms()->getVar('app_publisher')) {
					$publisher = $this->cms()->getVar('app_publisher');
				} else {
					$publisher = $this->config()->getConfig('publisher');
				}

				if ($this->cms()->getVar('app_note')) {
					$note = $this->cms()->getVar('app_note');
				} else {
					$note = $this->config()->getConfig('note');
				}

				if ($this->cms()->getVar('app_name')) {
					$appName = $this->cms()->getVar('app_name');
				} else {
					$appName = $this->config()->getConfig('appname');
				}

				if ($this->cms()->getVar('app_developer')) {
					$developer = $this->cms()->getVar('app_developer');
				} else {
					$developer = $this->config()->getConfig('developer');
				}

			} else {
				// TODO: getting module version from it's config. I need to make module config in yaml before it.
			}

			if (strlen($publisher) < 1) {

			} else {

			}

			if ($note) {
				$note = '-' . $note;
			}

			if ($developer) {
				if ($publisher) {
					$full = $appName . '/' . $version . $note . ' (' . $developer . '/' . $publisher . ')';
				} else {
					$full = $appName . '/' . $version . $note . ' (' . $developer . ')';
				}
			} elseif ($publisher) {
				$full = $appName . '/' . $version . $note . ' (' . $publisher . ')';
			} else {
				$full = $appName . '/' . $version . $note;
			}

			switch ($context) {
				case 'version':
					return $version;
					break;

				case 'note':
					return $note;
					break;

				case 'vNote':
					return $version . '-' . $note;
					break;

				default:
					return $full;
					break;
			}

		}

		public function intVersion($module = false)
		{
			if (!$module) {
				$v = explode('.', $this->version(false, 'version'));
			} else {
				// TODO: getting module version from it's config. I need to make module config in yaml before it.
			}

			$major = $v[0];
			$minor = $v[1];
			$release = $v[2];

			$major = $this->numericVersion($major);
			$minor = $this->numericVersion($minor);
			$release = $this->numericVersion($release);

			return $major . $minor . $release;

		}

		public function numericVersion($int)
		{
			if (strlen($int) == 5) {
				return $int;
			} elseif (strlen($int) == 4) {
				return '0' . $int;
			} elseif (strlen($int) == 3) {
				return '00' . $int;
			} elseif (strlen($int) == 2) {
				return '000' . $int;
			} elseif (strlen($int) == 1) {
				return '0000' . $int;
			} else {
				return 00000;
			}
		}

	}
