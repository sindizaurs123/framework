<?php

	class Config extends Core
	{
		public function __construct()
		{
			$this->config = array();

			$glob = '../config/default.yaml';
			$this->config = spyc_load_file($glob);

			$custom = '../config/config.yaml';

			if (file_exists($custom)) {
				$customConfig = spyc_load_file($custom);
			} else {
				$customConfig = array();
			}

			foreach ($customConfig as $key => $value) {
				$this->config[$key] = $value;
			}

		}
		/**
		 * Loads YAML file into an array.
		 *
		 * @param string $file path to YAML file relative of /public direcotry.
		 *
		 * @return array
		 */
		public function loadYAML($file)
		{
			return spyc_load_file('../config/' . $file);
		}

		public function cookiesValidFor()
		{
			if (is_int($this->getConfig('session_length'))) {
				return $this->getConfig('session_length');
			} else {
				return 7200;
			}
		}


		/** Gets system configuration node
		 *
		 * @param string $node Key
		 *
		 * @return mixed Returns config flag value if exists, null otherwise.
		 */
		public function getConfig($node)
		{
			if ($this->config[$node]) {
				return $this->config[$node];
			} else {
				return null;
			}
		}

		/** Gets system configuration node, alias of self::getConfig
		 *
		 * @param string $node Key
		 *
		 * @return mixed returns config flag if exists, null otherwise.
		 *
		 */
		public function get($node) {
			return $this->getConfig($node);
		}
	}