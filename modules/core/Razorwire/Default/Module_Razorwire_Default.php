<?php

	class Module_Razorwire_Default extends Modules
	{

		public function __construct($loader)
		{

			if ($loader === 'autoloader') {

				$this->loader()->addRouteItem('', 'Razorwire/Default');

				$this->lang()
					->nameLocale('en_GB', 'English (United Kingdom)')
					->nameLocale('lv_LV', 'Latvian')
					->loadFromCSV('core/default/en_GB', 'en_GB')
					->loadFromCSV('core/default/lv_LV', 'lv_LV')
					->setLocale('en_GB');

				$this->templates()
					->setTitle($this->__('RAZORWIRE_WELCOME_TEXT'))
					->setBlock('projectName', 'Razorwire')
					->setView('errorMessage', "$this/Views/Errors")
					->setView('nav-top', "$this/Views/nav-top")
					->setView('footer', "$this/Views/Footer")
					->setLayout("$this/Layouts/Default");

				$this->cms()->addMenubarItem('home', $this->__('HOME'), '/', 'left');
				$this->cms()->addMenubarItem('devmode', $this->__('DEV_DEBUG'), './/?dev', 'left');
				$this->cms()->addMenubarItem('auth_login', $this->__('LOGIN_BUTTON'), '/login', 'right');
				$this->cms()->addMenubarItem('dropdown', $this->__('TEST_DROPDOWN_MENU'), '/', 'right');
				$this->cms()->addMenubarItem('dropdown3', $this->__('TEST_DROPDOWN_ITEM_1'), '/', 'left', 'dropdown');
				$this->cms()->addMenubarItem('dropdown2', $this->__('TEST_DROPDOWN_ITEM_2'), '/', 'left', 'dropdown');

				$this->loader()->set404action('$core->redirect()');


			}

			$this->templates()
				->setTitle($this->__('RAZORWIRE_WELCOME_TEXT'))
				->setView('main', "$this/Views/Front")
				->setTemplate("$this/Templates/Index");

		}

		public function __toString()
		{
			return $this->getPath();
		}

	}