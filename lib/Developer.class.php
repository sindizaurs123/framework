<?php


	class Developer extends Core
	{
		public function __construct()
		{
			if ($this->config()->getConfig('environment') == 'dev') {
				$this->developerMode = true;
			} else {
				$this->developerMode = false;
			}
		}


		/** Enables developer mode
		 *
		 * @return true
		 */
		public function enableDeveloperMode()
		{
			$this->developerMode = true;

			return true;
		}

		/** Disables developer mode
		 *
		 * @return true;
		 */
		public function disableDeveloperMode()
		{
			$this->developerMode = false;

			return true;
		}

		/** Checks if developer mode is enabled.
		 *
		 * @return bool
		 */
		public function isDeveloperMode()
		{
			return $this->developerMode;
		}

		/** Dumps object using Kint class if in developer mode or force flag set
		 *
		 * @param mixed $param What to dump
		 * @param bool $force Force dumping?
		 *
		 * @return mixed Returns dump value if data dumped successfully, false otherwise.
		 */
		public function dump($param, $force = false)
		{
			if ($force or $this->developerMode) {
				return d($param);
			} else {
				return false;
			}
		}

		/** Prints stack trace if in developer mode or force flag set
		 *
		 * @param bool $force Force dumping?
		 *
		 * @return mixed Returns true if trace printed successfully, false otherwise.
		 */
		public function trace($force = false)
		{
			if ($force or $this->developerMode) {
				Kint::trace();

				return true;
			} else {
				return false;
			}
		}

		/** Prints system objects by level.
		 * @param int $level Debug level
		 * Level 1 - Modules, Templates, CMS
		 * Level 2 - Level 1 +Loader
		 * Level 3 - Level 2 + Config, Log
		 * Unspecified - Modules only
		 *
		 * @return mixed
		 */
		public function debug($level = 0)
		{
			switch ($level) {
				case 1:
					$this->dump($this->modules(), true);
					$this->dump($this->templates(), true);
					$this->dump($this->cms(), true);
					break;
				case 2:
					$this->dump($this->loader(), true);
					$this->dump($this->modules(), true);
					$this->dump($this->templates(), true);
					$this->dump($this->cms(), true);
					break;
				case 3:
					$this->dump($this->loader(), true);
					$this->dump($this->modules(), true);
					$this->dump($this->templates(), true);
					$this->dump($this->cms(), true);
					$this->dump($this->log(), true);
					$this->dump($this->config(), true);
					break;
				default:
					$this->dump($this->modules(), true);
					break;
			}

			return true;
		}

	}