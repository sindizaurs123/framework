<?php

	class Module_Sindizaurs_Test extends Modules
	{

		public function __construct($loader)
		{

			if ($loader === 'autoloader') {

				$this->loader()->addRouteItem('', 'Sindizaurs/Test');
				$this->loader()->addRouteItem('test123', 'Sindizaurs/Test');

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
				$this->cms()->addMenubarItem('test123', $this->__('LINK_MENUBAR_TEST123'), '/test123', 'left');

				$this->loader()->set404action('$core->redirect()');


			}

			if ($loader == 'route') {
				if ($this->url('test123')) {
					$this->test123();
				} else {
					$this->templates()
						->setTitle($this->__('RAZORWIRE_WELCOME_TEXT'))
						->setView('main', "$this/Views/Front")
						->setTemplate("$this/Templates/Index");
				}
			}
			
		}

		public function test123() {
			d('test123');
		}

		public function __toString()
		{
			return $this->getPath();
		}

	}