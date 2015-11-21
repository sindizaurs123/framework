<?php

	class Module_Sindizaurs_Yolo extends Modules
	{

		/**
		 * Possible values for $calledFrom:
		 * bool(false)              Loaded from different module using $this->modules()->getModule('Example/ExampleModule');
		 * string('autoloader')     Loaded from autoloader
		 * string('route')          Loaded from route
		 * string('cli')            Loaded from CLI
		 * @param mixed $calledFrom
		 *
		 * @returns mixed
		 */
		public function __construct($calledFrom = false)
		{
			if (!$calledFrom) {

				/**
				 * Module was called from another module for whatever reasons
				 */

			} elseif ($calledFrom === 'autoloader') {

				// pievieno template
				$this->templates()->setLayout("$this/layouts/layout420");
				//pievieno footer
				$this->templates()->setView('footer', "$this/layouts/footer");
				//tas pats
				$this->templates()->setBlock('test', 'kksdf');
				

				$this->lang()->nameLocale('en', 'English'); //valoda, nosaukums
				$this->lang()->loadFromYaml("$this/language", 'en'); //ieladē valodu no yaml faila
				$this->lang()->setLocale('en'); //uzstāda sistēmas valoda


				$this->templates()->setBlock('title', $this->__('PAGE_TITLE')); //default title, izvelk no language.yaml texta
				// $this->loader()->addRouteItem 1. parametrs - HTTP adrese aiz šķerssvītras. 2. parametrs - Modulis, kuru ielādēt.
				$this->loader()->addRouteItem('', 'Sindizaurs/Yolo');
				$this->loader()->addRouteItem('test', 'Sindizaurs/Yolo');
				$this->loader()->addRouteItem('test2', 'Sindizaurs/Yolo');

				$this->loader()->addRouteItem('test3','', true, 'https://google.com/');
				/**
				 * Module was called from autoloader
				 */

			} elseif ($calledFrom === 'route') {

				/**
				 * Module was called ny route
				 */

				if ($this->url('test')) {
					// Ja adrese ir /test, izdarīt šo.
					$this->templates()->setBlock('title', '/test');

					if ($this->url(2) == 'kautkas') {

						$this->templates()->setBlock('title', '/test/kautkas');

					} elseif ($this->url(2) == 'memes') {

						$this->templates()->setBlock('title', '/test/memes');

					} elseif (!$this->url(2)) {

						$this->templates()->setBlock('title', '/test');

					} else {

						$this->templates()->setBlock('title', '/test/default');

					}
				}

				if ($this->url('test2')) {
					$this->templates()->setLayout("$this/layouts/layout421");
				}

			} elseif ($calledFrom === 'cli') {

				/**
				 * Module was called from cli
				 */

			} else {

				/**
				 * This generally shouldn't happen.
				 */

				return false;
			}

			return true;
		}

		public function __toString()
		{
			return $this->getPath();
		}

	}