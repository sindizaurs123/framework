<?php

	class CMS extends Core
		{
			
			public function __construct()
				{
					$this->storedVars = array();
					$this->menubar = array();
					$this->definedMenubarItems = array();
				}


			/* 
			 * Menubar functions
			 */

			public function addMenubarItem($name, $title, $href, $position = 'left', $parent = false, $class = '', $id = '')
				{
					$this->definedMenubarItems[$name] = true;
					if (!$parent)
						{
							$this->menubar[$name] = array(
								'name' 	=> $name,
								'title' 	=> $title,
								'href' 	=> $href,
								'class'	=> $class,
								'id' 		=> $id,
								'parent' => false,
								'position' => $position
							);

							return true;
						}
					else
						{
							if ($this->menubarItemExists($parent))
								{
									if ($this->menubar[$parent])
										{
											$this->menubar[$parent]['childs'][$name] = array(
												'name' => $name,
												'title'=> $title,
												'href' => $href,
												'class'=> $class,
												'id'   => $id,
												'parent' => $parent,
											);
										}
									else
										{
											foreach ($this->menubar as $varName => $value)
												{
													foreach ($value['childs'] as $child => $value)
														{
															if ($value['name'] == $parent)
																{
																	$_parent = $value['parent'];
																	$_name = $value['name'];

																	$this->menubar[$_parent]['childs'][$_name]['childs'][$name] = array(
																			'name' => $name,
																			'title'=> $title,
																			'href' => $href,
																			'class'=> $class,
																			'id'   => $id,
																			'parent' => $parent,
																		);
																	
																}
														}
												}
										}

									return true;
								}
							else
								{
									return false;
								}
						}
					
				}

			public function destroyMenubarItem($name)
				{
					unset($this->menubar[$name]);
					unset($this->definedMenubarItems[$name]);
					return $this;
				}

			public function menubarItemExists($name)
				{
					if ($this->definedMenubarItems[$name])
						{
							return true;
						}
					else
						{
							return false;
						}
				}

			public function getMenubarItem($name)
				{
					if ($this->menubarItemExists($name))
						{
							return $this->menubar[$name];
						}
					else
						{
							return false;
						}
				}

			public function getMenubar()
				{
					return $this->menubar;
				}

			public function resetMenubar()
				{
					$this->menubar = array();
					return true;
				}

			/* 
			 * Session variables
			 */

			public function setSessionVar($name, $content)
				{
					$_SESSION[$name] = $content;
				}

			public function unsetSessionVar($name)
				{
					unset($_SESSION[$name]);
				}

			public function sessionVarExists($name)
				{
					if (isset($_SESSION[$name]))
						{
							return true;
						}
					else
						{
							return false;
						}
				}

			public function getSessionVar($name)
				{
					if ($this->sessionVarExists($name))
						{
							return $_SESSION[$name];
						}
					else
						{
							return null;
						}
				}

			/*
			 * Globally accessible variables
			 */

			public function setVar($name, $content)
				{
					$this->storedVars[$name] = $content;
				}

			public function unsetVar($name)
				{
					unset($this->storedVars[$name]);
				}

			public function varExists($name)
				{
					if (isset($this->storedVars[$name]))
						{
							return true;
						}
					else
						{
							return false;
						}
				}

			public function getVar($name)
				{
					if ($this->varExists($name))
						{
							return $this->storedVars[$name];
						}
					else
						{
							return null;
						}
				}

			/* 
			 * Cookies
			 */

			public function safeUrl($string, $lower = true, $remove_dashes = true) {
				$translit = array(
					'/ä|æ|ǽ/' => 'ae',
					'/ö|œ/' => 'oe',
					'/ü/' => 'ue',
					'/Ä/' => 'Ae',
					'/Ü/' => 'Ue',
					'/Ö/' => 'Oe',
					'/À|Á|Â|Ã|Ä|Å|Ǻ|Ā|Ă|Ą|Ǎ|А/' => 'A',
					'/à|á|â|ã|å|ǻ|ā|ă|ą|ǎ|ª|а/' => 'a',
					'/б/' => 'Б',
					'/б/' => 'b',
					'/Ç|Ć|Ĉ|Ċ|Č|Ц|Ч/' => 'C',
					'/ç|ć|ĉ|ċ|č|ц|ч/' => 'c',
					'/Ð|Ď|Đ|Д/' => 'D',
					'/ð|ď|đ|д/' => 'd',
					'/È|É|Ê|Ë|Ē|Ĕ|Ė|Ę|Ě|Ё|Е|З|Э/' => 'E',
					'/è|é|ê|ë|ē|ĕ|ė|ę|ě|ё|е|з|э/' => 'e',
					'/Ф/' => 'F',
					'/ф/' => 'f',
					'/Ĝ|Ğ|Ġ|Ģ|Г/' => 'G',
					'/ĝ|ğ|ġ|ģ|г/' => 'g',
					'/Ĥ|Ħ|Х/' => 'H',
					'/ĥ|ħ|х/' => 'h',
					'/Ì|Í|Î|Ï|Ĩ|Ī|Ĭ|Ǐ|Į|İ|И|Й|Ы/' => 'I',
					'/ì|í|î|ï|ĩ|ī|ĭ|ǐ|į|ı|и|й|ы/' => 'i',
					'/Ĵ|Ъ/' => 'J',
					'/ĵ|ъ/' => 'j',
					'/Ķ|К/' => 'K',
					'/ķ|к/' => 'k',
					'/Ĺ|Ļ|Ľ|Ŀ|Ł|Л/' => 'L',
					'/ĺ|ļ|ľ|ŀ|ł|л/' => 'l',
					'/М/' => 'M',
					'/м/' => 'm',
					'/Ñ|Ń|Ņ|Ň|Н/' => 'N',
					'/ñ|ń|ņ|ň|ŉ|н/' => 'n',
					'/Ò|Ó|Ô|Õ|Ō|Ŏ|Ǒ|Ő|Ơ|Ø|Ǿ|О/' => 'O',
					'/ò|ó|ô|õ|ō|ŏ|ǒ|ő|ơ|ø|ǿ|º|о/' => 'o',
					'/П/' => 'P',
					'/п/' => 'p',
					'/Ŕ|Ŗ|Ř|Р/' => 'R',
					'/ŕ|ŗ|ř|р/' => 'r',
					'/Ś|Ŝ|Ş|Š|С|Ш|Щ/' => 'S',
					'/ś|ŝ|ş|š|ſ|с|ш|щ/' => 's',
					'/Ţ|Ť|Ŧ|Т/' => 'T',
					'/ţ|ť|ŧ|т/' => 't',
					'/Ù|Ú|Û|Ũ|Ū|Ŭ|Ů|Ű|Ų|Ư|Ǔ|Ǖ|Ǘ|Ǚ|Ǜ|У/' => 'U',
					'/ù|ú|û|ũ|ū|ŭ|ů|ű|ų|ư|ǔ|ǖ|ǘ|ǚ|ǜ|у/' => 'u',
					'/В/' => 'V',
					'/в/' => 'v',
					'/Ý|Ÿ|Ŷ/' => 'Y',
					'/ý|ÿ|ŷ/' => 'y',
					'/Ŵ/' => 'W',
					'/ŵ/' => 'w',
					'/Ź|Ż|Ž|Ж/' => 'Z',
					'/ź|ż|ž|ж/' => 'z',
					'/Æ|Ǽ/' => 'AE',
					'/ß/' => 'ss',
					'/Ĳ/' => 'IJ',
					'/ĳ/' => 'ij',
					'/Œ/' => 'OE',
					'/ƒ/' => 'f',
					'/Ю/' => 'Ju',
					'/ю/' => 'ju',
					'/Я/' => 'Ja',
					'/я/' => 'ja'
				);

				$string = trim($string);
				$string = preg_replace(array_keys($translit), array_values($translit), $string);
				$string = str_replace('&amp;', '-un-', $string);
				$string = str_replace(array(' ', '.', ',', '"', '=', '`', ']', '[', '|', ':', '+', '&quot;', '!', '/', "\\"), '-', $string);
				$allowed = "/[^a-z0-9\\-\\_\\\\]/i";
				$string = preg_replace($allowed, '', $string);

				//remove repeated dashes
				$string = preg_replace('/-+/', '-', $string);

				//remove dashes from ends of string
				if ($remove_dashes) {
					$string = trim($string, '-');
				}

				$string = substr($string, 0, 100);
				if (empty($string)) {
					$string = 'page';
				}
				if ($lower) {
					$string = strtolower($string);
				}
				return $string;
			}
		}