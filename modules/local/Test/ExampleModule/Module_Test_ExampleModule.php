<?php

	class Module_Test_ExampleModule extends Modules
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

				/**
				 * Module was called from autoloader
				 */

			} elseif ($calledFrom === 'route') {

				/**
				 * Module was called ny route
				 */

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

	}