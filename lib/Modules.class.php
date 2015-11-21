<?php

	class Modules extends Core
	{

		public function __construct()
		{
			/** Array of all coderoots, containing all modules. */
			$this->modules = array();

			/** Array of modules that can be loaded from CLI */
			$this->cli = array();

			/** Array of modules to be autoloaded from CLI */
			$this->cli_autoload = array();

			/** Array of modules to autoload */
			$this->autoload = array();

			/** Array of loaded modules */
			$this->loaded = array();

			/** Array of stored module methods */
			$this->methods = array();

			/** Array of stored module class names */
			$this->classes = array();

			/** Array of modules that are disabled in runtime */
			$this->runtimeDisabled = array();

			/** Array of modules to be overrided
			 * i.e. if there's an override set to override Razorwire/Test to Developer/Test, load Developer/Test, instead of Razorwire/Test
			 */
			$this->overrides = array();

		}

		/** Bootstrap. Builds an array of all modules and autoloads what needs to be autoloaded.
		 *
		 * @return true
		 */
		public function bootstrap()
		{

			/** Build an array of all modules */
			$this->getAllModules();
			if ($this->isCLI()) {
				// Different bootstrap for command line applications
			} else {
				$autoload = $this->getAutoload();

				foreach ($autoload as $path => $priority) {
					@$this->loadModule($path, 'autoloader');
				}
			}

			return true;

		}

		/**
		 * Checks if module is disabled in runtime.
		 *
		 * @param string $moduleName Module Name
		 *
		 * @return bool
		 */
		public function isRuntimeDisabled($moduleName)
		{
			if (isset($this->runtimeDisabled[$moduleName])) {
				if ($this->runtimeDisabled[$moduleName] === true) {
					return true;
				} else {
					return false;
				}
			} else {
				return false;
			}
		}

		/**
		 * Disables module in runtime
		 *
		 * @param string $moduleName Module Name
		 *
		 * @return true
		 */
		public function disableModule($moduleName)
		{
			$this->runtimeDisabled[$moduleName] = true;

			return true;
		}

		/**
		 * Enables module in runtime
		 *
		 * @param string $moduleName Module Name
		 *
		 * @return true
		 */
		public function enableModule($moduleName)
		{
			$this->runtimeDisabled[$moduleName] = false;

			return true;
		}

		/**
		 * Adds an override for module
		 *
		 * @param string $moduleName Module Name (for module to be overriden)
		 * @param string $override   Module Name (for module that overrides)
		 *
		 * @return true
		 */
		public function addOverride($moduleName, $override)
		{
			$this->overrides[$moduleName] = $override;

			return true;
		}

		/**
		 * Checks if module is being overriden. Returns overriding modules' name if yes, null otherwise.
		 *
		 * @param string $moduleName Module Name
		 *
		 * @return mixed
		 */
		public function getOverride($moduleName)
		{
			if (isset($this->overrides[$moduleName])) {
				return $this->overrides[$moduleName];
			} else {
				return null;
			}
		}

		/**
		 * Destroys previously set override flag.
		 *
		 * @param string $moduleName Module Name
		 *
		 * @return bool true if override destroyed, false if there was nothing to destroy
		 */
		public function destroyOverride($moduleName)
		{
			if ($this->getOverride($moduleName) !== null) {
				unset($this->overrides[$moduleName]);

				return true;
			}

			return false;
		}

		/**
		 * Gets Coderoot priority.
		 * Local priority       : 0
		 * Community priority   : 1'000'000
		 * Core priority        : 2'000'000
		 *
		 * @param string $codeRoot Coderoot (i.e. local/community/core)
		 *
		 * @return int
		 */
		private function getCodeRootPriority($codeRoot)
		{
			switch ($codeRoot) {
				case 'local':
					return 0;
					break;
				case 'community':
					return 1000000;
					break;
				case 'core':
					return 2000000;
					break;
			}

			return -1;
		}

		/**
		 * Builds an array of all modules and stores it in $this->modules
		 *
		 * @return true
		 */
		private function getAllModules()
		{

			$modules = array(
				'local'     => array(),
				'community' => array(),
				'core'      => array(),
			);


			foreach ($modules as $key => $val) {
				$priority = 1000;
				foreach (glob('../modules/' . $key . '/*') as $vendor) {
					if (is_dir($vendor)) {

						$path = $vendor;
						$vendor = str_replace("../modules/$key/", '', $path);

						$metaPath = $path . '/meta.yaml';

						if (file_exists($metaPath)) {
							$metaFile = $this->config()->loadYAML($metaPath);
						} else {
							$metaFile = array();
						}


						if (isset($metaFile['priority'])) {
							$vendorPriority = $metaFile['priority'];
						} else {
							$vendorPriority = $priority;
							$priority += 1000;
						}

						$vendorPriority += $this->getCodeRootPriority($key);

						$modules[$key] += array(
							$vendor => array(
								'vendor'   => $vendor,
								'path'     => $path,
								'priority' => $vendorPriority,
								'modules'  => array(),
							)
						);

						$modules[$key][$vendor] += $metaFile;

						$modules[$key][$vendor]['modules'] = $this->getModulesByVendor($key, $vendor, $vendorPriority);


					}
				}

				$modules[$key] = $this->organizeArrayByPriorities($modules[$key]);
			}

			$this->modules = $modules;

			return true;

		}

		/**
		 * Builds an array of modules by vendor.
		 *
		 * @param string $codeRoot       Code root
		 * @param string $vendor         Vendor
		 * @param int    $vendorPriority Vendor priority
		 *
		 * @return true
		 */
		private function getModulesByVendor($codeRoot, $vendor, $vendorPriority)
		{
			$modules = array();

			$priority = 10;
			foreach (glob("../modules/$codeRoot/$vendor/*") as $module) {
				if (is_dir($module)) {
					$path = $module;
					$module = str_replace("../modules/$codeRoot/$vendor/", '', $path);

					$definitionYaml = $path . '/module.yaml';
					$configYaml = $path . '/config.yaml';

					if (file_exists($definitionYaml)) {
						$definitionYaml = $this->config()->loadYAML($definitionYaml);

						if (isset($definitionYaml['priority']) && $definitionYaml['priority'] > 0) {
							$modulePriority = $vendorPriority + $definitionYaml['priority'];
						} else {
							$modulePriority = $vendorPriority + $priority;
							$priority += 10;
						}

						if ($definitionYaml['autoload']) {
							$autoload = true;
						} else {
							$autoload = false;
						}
					} else {
						$definitionYaml = array();
						$modulePriority = $vendorPriority + $priority;
						$priority += 10;
						$autoload = false;
					}

					if (file_exists($configYaml)) {
						$configYaml = $this->config()->loadYAML($configYaml);
					} else {
						$configYaml = array();
					}

					$modules[$module] = array(
						'module'     => $module,
						'vendor'     => $vendor,
						'path'       => $path,
						'controller' => 'Module_' . $vendor . '_' . $module,
						'priority'   => $modulePriority,
						'autoload'   => $autoload,
					);


					if ($autoload) {
						$this->addToAutoload("$vendor/$module", $modulePriority);
					}


					$modules[$module] += $definitionYaml;
					$modules[$module] += $configYaml;


				}
			}

			$modules = $this->organizeArrayByPriorities($modules);

			return $modules;

		}

		/**
		 * Adds module to autoload.
		 *
		 * @param string $moduleName ModuleName
		 * @param int    $priority   Priority
		 *
		 * @return true
		 */
		private function addToAutoload($moduleName, $priority)
		{
			$this->autoload[$moduleName] = $priority;
			asort($this->autoload);

			return true;

		}

		/**
		 * Returns an array of modules in autoload.
		 *
		 * @return array
		 */
		public function getAutoload()
		{
			return $this->autoload;
		}

		/**
		 * Organizes 3 dimensional vendor/module arrays by priority keys.
		 *
		 * @param array $array Array of modules or coderoots.
		 *
		 * @return array $array
		 */
		private function organizeArrayByPriorities($array)
		{

			uasort($array, function ($a, $b) {

				$a = $a['priority'];
				$b = $b['priority'];

				if ($a == $b) {
					return 0;
				}

				return ($a < $b) ? -1 : 1;

			});

			return $array;
		}

		/**
		 * Checks if module exists
		 *
		 * @param string $moduleName Module name
		 * @return bool|string Returns false if not, coderoot if yes.
		 */
		public function moduleExists($moduleName)
		{
			return $this->getModuleCodeRoot($moduleName);
		}

		/**
		 * Gets modules' coderoot
		 *
		 * @param string $moduleName Module Name
		 *
		 * @return bool|string false if module doesn't exist, coderoot otherwise.
		 */
		public function getModuleCodeRoot($moduleName)
		{

			if (file_exists('../modules/local/' . $moduleName)) {
				return 'local';
			} elseif (file_exists('../modules/community/' . $moduleName)) {
				return 'community';
			} elseif (file_exists('../modules/core/' . $moduleName)) {
				return 'core';
			} else {
				return false;
			}
		}

		/**
		 * Gets modules' path.
		 *
		 * @param string $moduleName Module Name
		 *
		 * @return bool|string false if module doesn't exist, path otherwise.
		 */
		public function getModulePath($moduleName)
		{
			$codeRoot = $this->getModuleCodeRoot($moduleName);
			if ($codeRoot) {
				return "../modules/$codeRoot/$moduleName";
			} else {
				return false;
			}

		}

		/**
		 * Gets modules' preload file.
		 *
		 * @param string $moduleName Module Name
		 * @return bool|string false if no preload, filename otherwise.
		 */
		public function getModulePreload($moduleName)
		{
			$modulePath = $this->getModulePath($moduleName);
			$preloadFile = $modulePath . '/preload.php';

			if (file_exists($preloadFile)) {
				return $preloadFile;
			} else {
				return false;
			}
		}

		/**
		 * Gets modules' config from library ($this->modules array)
		 * @param $path
		 * @return mixed
		 */
		public function getModuleConfigFromLibrary($moduleName)
		{
			$codeRoot = $this->getModuleCodeRoot($moduleName);
			$data = explode('/', $moduleName);

			return $this->modules[$codeRoot][$data[0]]['modules'][$data[1]];
		}

		/** Alias of getConfig
		 *
		 * @param string $moduleName Module Name
		 * @return array Config
		 */
		public function getModuleConfig($moduleName = null)
		{
			return $this->getConfig($moduleName);
		}

		/** Gets current modules' path (in context of $this->getPath() inside a module)
		 *
		 * @return string Path
		 */
		public function getPath()
		{
			$file = str_replace('\\', '/', __FILE__);
			$modulePath = str_replace('\\', '/', debug_backtrace()[0]['file']);
			$root = str_replace('/lib/Modules.class.php', '', $file) . '/modules';
			$path = str_replace($root, '', $modulePath);
			$pathArray = explode('/', $path);

			$codeRoot = $pathArray[1];
			$vendor = $pathArray[2];
			$module = $pathArray[3];

			return "../modules/$codeRoot/$vendor/$module";
		}


		/**
		 * Gets modules' config
		 * If no $moduleName is specified, returns current modules' config.
		 *
		 * @param mixed $moduleName
		 *
		 * @return array Config
		 */
		public function getConfig($moduleName = null)
		{
			if ($moduleName) {
				$codeRoot = $this->getModuleCodeRoot($moduleName);
				$pathArray = explode('/', $moduleName);

				$vendor = $pathArray[0];
				$module = $pathArray[1];
				$path = "../modules/$codeRoot/$moduleName";

				$definitionYaml = $path . '/module.yaml';
				$configYaml = $path . '/config.yaml';

				$config = array(
					'module'     => $module,
					'vendor'     => $vendor,
					'path'       => $path,
					'controller' => 'Module_' . $vendor . '_' . $module,
				);

				if (file_exists($definitionYaml)) {
					$config += $this->config()->loadYAML($definitionYaml);
				}

				if (file_exists($configYaml)) {
					$config += $this->config()->loadYAML($configYaml);
				}

				return $config;

			} else {
				$file = str_replace('\\', '/', __FILE__);
				$modulePath = str_replace('\\', '/', debug_backtrace()[0]['file']);
				$root = str_replace('/lib/Modules.class.php', '', $file) . '/modules';
				$path = str_replace($root, '', $modulePath);
				$pathArray = explode('/', $path);

				$codeRoot = $pathArray[1];
				$vendor = $pathArray[2];
				$module = $pathArray[3];

				return $this->getConfig("$vendor/$module");
			}
		}

		/** Initializes module
		 *
		 * @param string $moduleName Module Name
		 *
		 * @return mixed Array of config if module exists/can be loaded, false otherwise.
		 */
		private function load($moduleName)
		{
			if ($this->isRuntimeDisabled($moduleName) or $this->getConfig($moduleName)['disabled']) {
				// TODO: Log message
				return false;
			} elseif ($this->moduleExists($moduleName)) {
				$codeRoot = $this->getModuleCodeRoot($moduleName);
				$preload = $this->getModulePreload($moduleName);
				$config = $this->getModuleConfig($moduleName);
				$fullPath = '../modules/' . $codeRoot . '/' . $moduleName;

				if ($preload) {
					require $preload;
				}

				if(file_exists($fullPath. '/module.php')) {
					require $fullPath . '/module.php';
				} else {
					require $fullPath . "/Module_".str_replace('/', '_', $moduleName).'.php';
				}


				$this->loaded[$moduleName] = true;

				return $config;
			} else {
				// TODO: Log message
				return false;
			}
		}

		/**
		 * Loads module
		 *
		 * @param string $moduleName Module Name
		 * @param mixed $loader What is loading this module (i.e. autoloader/route/false)
		 *
		 * @return bool|object Returns module if loaded successfully, false otherwise.
		 */
		private function loadModule($moduleName, $loader = false)
		{
			if ($this->isCLI()) {
				$loader = 'cli';
			}

			if (isset($this->loaded[$moduleName])) {
				return true;
			}

			$overridePath = false;

			if (isset($this->overrides[$moduleName])) {
				$overridePath = $this->overrides[$moduleName];
			}


			$module = $this->load($moduleName);
			$class = $module['controller'];

			if (class_exists($class)) {
				$this->setModulePath($class, $moduleName);
				$this->loaded[$moduleName] = true;

				if ($overridePath) {

					@$override = $this->loadModule($overridePath, $loader);
					$overrideClass = get_class($override);

					if (class_exists($class)) {

						$this->loaded[$override] = true;

						@$this->storeMethod($overridePath, $override);
						@$this->storeMethod($moduleName, $override);

						$this->setModulePath($class, $overridePath);
						$this->setModulePath($overrideClass, $overridePath);

						return $override;
					} else {
						return false;
					}

				} else {
					$module = new $class($loader);
					@$this->storeMethod($moduleName, $module);
				}

				return $module;
			} else {
				return false;
			}

		}

		/**
		 * Checks if specific file within modules' directory exists.
		 *
		 * @param string $moduleName Module Name
		 * @param string $fileName File Name
		 * @return bool True if exists, false otherwise.
		 */
		public function moduleFileExists($moduleName, $fileName)
		{
			$filePath = '../modules/' . $this->getModuleCodeRoot($moduleName) . '/' . $moduleName . '/' . $fileName;

			if (file_exists($filePath)) {
				return true;
			} else {
				return false;
			}
		}

		/**
		 * Gets full path of specific file withing modules' directory.
		 *
		 * @param string $moduleName Module Name
		 * @param  string $fileName File Name
		 * @return bool|string Full path if module exists, false otherwise.
		 */
		public function getModuleFilePath($moduleName, $fileName)
		{
			if ($this->moduleFileExists($moduleName, $fileName)) {
				$filePath = '../modules/' . $this->getModuleCodeRoot($moduleName) . '/' . $moduleName . '/' . $fileName;

				return $filePath;
			} else {
				return false;
			}
		}

		/**
		 * Checks if module is loaded.
		 *
		 * @param string $moduleName Module name
		 *
		 * @return bool True if loaded, false otherwise.
		 */
		public function isLoaded($moduleName)
		{
			if ($this->loaded[$moduleName] === true) {
				return true;
			} else {
				return false;
			}
		}


		/**
		 * Stores modules' object in an array.
		 *
		 * @param string $moduleName Module Name
		 * @param object $method Object
		 *
		 * @return bool
		 */
		private function storeMethod($moduleName, $method)
		{
			if ($this->methods[$moduleName] = $method) {
				$this->classes[get_class($method)] = $moduleName;

				return true;
			} else {
				return false;
			}
		}

		/**
		 * Checks if modules' method is stored.
		 *
		 * @param string $moduleName Module Name
		 *
		 * @return bool
		 */
		public function isStored($moduleName)
		{
			if (isset($this->methods[$moduleName])) {
				return true;
			} else {
				return false;
			}
		}

		/**
		 * Returns modules' method.
		 *
		 * @param string $moduleName Module Name
		 * @return object|false Returns object if modules' method is stored, false otherwise.
		 */
		public function getMethod($moduleName)
		{
			if ($this->isStored($moduleName)) {
				return $this->methods[$moduleName];
			} else {
				return false;
			}
		}

		/**
		 * Gets module.
		 *
		 * @param string $moduleName Module Name
		 * @param string|false $loader Loader
		 *
		 * @return bool|object False if module can't be loaded/doesn't exist, Object otherwise.
		 */
		public function getModule($moduleName, $loader = false)
		{
			if ($this->isStored($moduleName)) {
				return $this->methods[$moduleName];
			} else {
				return @$this->loadModule($moduleName, $loader);
			}
		}

		/** Sets modules' classpath
		 *
		 * @param string $className Class
		 * @param string $moduleName Module Name
		 *
		 * @return self
		 */
		public function setModulePath($className, $moduleName)
		{
			$this->classes[$className] = $moduleName;

			return $this;
		}


	}