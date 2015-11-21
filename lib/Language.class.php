<?php

	class Language extends Core
	{
		public function __construct()
		{
			$this->lang = array();
			$this->locales = array(
				'en_GB' => 'English',
			);

			$this->defaultLanguage = $this->config()->getConfig('default_language');
			$this->fallbackLanguage = $this->config()->getConfig('fallback_language');

			if (!$this->localeExists($this->defaultLanguage)) {
				$this->defaultLanguage = 'en_GB';
			}

			if (!$this->cms()->getVar('language')) {
				$this->cms()->setVar('language', $this->defaultLanguage);
			}

			if ($this->cms()->getVar('lang_enable_fallback') === null) {
				$this->cms()->setVar('lang_enable_fallback', true);
			} else {
				if ($this->cms()->getVar('lang_enable_fallback')) {
					$this->fallbach = true;
				} else {
					$this->fallback = false;
				}
			}

			if (!isset($this->fallback)) {
				$this->fallback = false;
			}

			$fallback = $this->cms()->getVar('fallback_language');

			if ($fallback === null) {
				$this->cms()->setVar('fallback_language', $this->fallbackLanguage);
			} else {
				if ($this->localeExists($fallback)) {
					$this->fallbackLanguage = $fallback;
				}
			}

		}

		/** Sets system locale
		 *
		 * @param string $locale locale name
		 *
		 * @return true
		 */
		public function setLocale($locale)
		{
			$this->cms()->setVar('language', $locale);

			return true;
		}

		/** @return string System Locale */
		public function getLocale()
		{
			return $this->cms()->getVar('language');
		}

		/** @return bool Whether locale exists */
		public function localeExists($locale)
		{
			if (isset($this->locales[$locale])) {
				return true;
			} else {
				return false;
			}
		}

		/** @return array Array of system locales */
		public function availableLocales()
		{
			return $this->locales;
		}

		/**
		 * Adds a name to system locale
		 *
		 * @param string $locale Locale short string
		 * @param string $name Language name
		 *
		 * @return self
		 *
		 * */
		public function nameLocale($locale, $name)
		{
			$this->locales[$locale] = $name;

			return $this;
		}

		/** Loads language strings from CSV
		 *
		 * @param string $file Path of CSV file to be loaded, relative of /language
		 * @param string $language Language name
		 *
		 * @return self
		 */
		public function loadFromCSV($file, $language)
		{
			$file = '../language/' . $file . '.csv';

			if (file_exists($file)) {
				$handle = fopen($file, 'r');
				while ($line = fgets($handle)) {
					$csv = str_getcsv($line);
					$node = $csv[0];
					$value = $csv[1];
					$this->set($node, $language, $value);
				}
				if (!$this->localeExists($language)) {
					$this->nameLocale($language, $language);
				}

				return $this;
			} else {
				return $this;
			}
		}

		/** Loads language strings from YAML
		 *
		 * @param string $file Path of YAML file to be loaded, relative of /language
		 * @param string $language Language name
		 *
		 * @return self
		 */
		public function loadFromYAML($file, $language)
		{
			$file = '../language/' . $file . '.yaml';

			if (file_exists($file)) {
				$yaml = spyc_load_file($file);
				foreach ($yaml as $node => $value) {
					$this->set($node, $language, $value);
				}

				if (!$this->localeExists($language)) {
					$this->nameLocale($language, $language);
				}

				return $this;
			} else {
				return $this;
			}
		}

		/**
		 * Translates language key to actual string.
		 *
		 * @param string $string System language string, i.e. SYSTEM_SOMETHING_TEST
		 * @param string $language Language key i.e. en_GB. Suggested to leave blank as that will be the set system language.
		 *
		 * @return string decoded language string.
		 *
		 */
		public function __($string, $language = false)
		{
			if ($language === false) {
				$language = $this->cms()->getVar('language');

				if ($language === false) {
					$language = 'en_GB';
				}
			}

			if ($this->lang[$language]) {
				if (isset($this->lang[$language][$string])) {
					return $this->lang[$language][$string];
				} else {
					if ($this->fallback) {
						return $this->lang[$this->fallbackLanguage][$string];
					} else {
						return $string;
					}
				}
			} else {
				return $string;
			}

		}

		/** Checks if translation for specific language key exists
		 *
		 * @param string $string System language key
		 * @param string $language Language key
		 *
		 * @return bool Whether this string can be translated.
		 */
		public function translationExists($string, $language = false)
		{
			if ($this->__($string, $language) == $string) {
				return false;
			} else {
				return true;
			}
		}

		/** Checks if language exists / is defined in system
		 *
		 * @param string $language System language key
		 *
		 * @return bool Whether language is defined
		 */
		public function languageExists($language)
		{
			if (isset($this->lang[$language])) {
				return true;
			} else {
				return false;
			}
		}

		/** Creates blank array of system languages.
		 *
		 * @param string $language Language name
		 *
		 * @return bool Whether language is defined
		 */
		public function createLanguage($language)
		{
			if (!$this->languageExists($language)) {
				$this->lang[$language] = array();

				return true;
			} else {
				return false;
			}
		}

		/** Sets translation of a system language key
		 *
		 * @param string $language System language key
		 * @param string $language Language key
		 * @param string $translation Translation to human readable text
		 *
		 * @return bool Whether language is defined
		 */
		public function setTranslation($string, $language, $translation)
		{
			if ($this->languageExists($language)) {
				$this->lang[$language][$string] = $translation;

				return $this;
			} else {
				$this->createLanguage($language);
				$this->lang[$language][$string] = $translation;

				return $this;
			}
		}

		/** @return self::setTranslation */
		public function set($string, $language, $translation)
		{
			return $this->setTranslation($string, $language, $translation);
		}

	}