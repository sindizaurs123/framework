<?php

	class Templates extends Core
		{

			public function __construct()
				{
					$this->content = array();
					$this->title = 'Razorwire Default Title';
					$this->css = array();
					$this->headJS = array();
					$this->footJS = array();
					$this->meta = array();
					$this->layout = array();
					$this->view = array();
					$this->template = false;
				}

			public function setTemplate($template)
				{
					$this->template = $template;
				}

			public function printTemplate()
				{
					include('../templates/'.$this->template.'.phtml');
				}

			public function templateName()
				{
					return $this->template;
				}

			public function setLayout($name)
				{
					$this->layout['name'] = $name;
				}

			public function printLayout()
				{
					include('../layouts/'.$this->layout['name'].'.phtml');
				}

			public function setBlock($block, $content)
				{
					$this->content[$block] = $content;
					return $this;
				}

			public function getBlock($block)
				{
					if ($this->isBlock($block))
						{
							return $this->content[$block];
						}
					else
						{
							return null;
						}
				}
			public function isBlock($block)
				{
					if (isset($this->content[$block]))
						{
							return true;
						}
					else
						{
							return false;
						}
				}

			public function setTitle($title)
				{
					$this->title = $title;
					return $this;
				}

			public function getTitle()
				{
					return $this->title;
				}

			public function setCSS($name)
				{
					$this->css[$name] = true;
					return $this;
				}

			public function getCSS()
				{
					$output = "\r\n";

					foreach ($this->css as $stylesheet => $status)
						{
							$output = $output . "\t\t<link rel=\"stylesheet\" href=\"$stylesheet\" type=\"text/css\" />\r\n";
						}
					return $output;
				}

			public function setJS($name, $pos)
				{
					if ($pos == 'head')
						{
							$this->headJS[$name] = true;
						}
					elseif ($pos == 'foot')
						{
							$this->footJS[$name] = true;
						}

					return $this;

				}

			public function getJS($pos)
				{
					$output = "\r\n";
					if ($pos == 'head')
						{
							foreach ($this->headJS as $js => $status)
								{
									$output = $output . "\t\t<script type=\"text/javascript\" src=\"$js\"></script>\r\n";
								}

							return $output;
						}

					elseif ($pos == 'foot')
						{
							foreach ($this->footJS as $js => $status)
								{
									$output = $output . "\t\t<script type=\"text/javascript\" src=\"$js\"></script>\r\n";
								}

							return $output;
						}
				}

			public function setView($name, $path)
				{
					$this->view[$name] = $path;
					return $this;
				}

			public function getView($name)
				{
					include('../views/'.$this->view[$name].'.phtml');
				}

			public function fGetView($name)
				{
					include('../views/'.$name.'.phtml');
				}
		}
