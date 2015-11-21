<?php

	class CLI extends Core
		{

			/*
			 * Class for creating CLI tools and stuff.
			 * Upon loading index.php from console __construct() gets called and does it's stuff.
			 */

			public function __construct()
				{
					$this->prompt = $this->config()->getConfig('default_cli_prompt');

					if ($this->isCLI())
						{
							$this->printLine('Welcome to Razorwire command line.');
							$this->modules()->bootstrap();
							$this->commandLine();
						}
				}

			public function printLine($line = '', $newline = true)
				{
					if ($newline)
						{
							$newline = "\r\n";
						}
					else
						{
							$newline = '';
						}
					echo "$line"."$newline";
				}

			public function commandLine()
				{
					$application = true;

					while($application)
						{
							$this->printLine($this->getPrompt(), false);
							$input = $this->getInput();

							$inputArray = explode(' ', $input);
							$command = $inputArray[0];

							$argumentArray = $inputArray;
							unset($argumentArray[0]);

							if (!empty($argumentArray))
								{
									if (count($argumentArray) > 1)
										{
											$arguments = '';

											foreach ($argumentArray as $key => $arg)
												{
													$arguments .= ' '.$arg;
												}

											$arguments = trim($arguments);
										}

									else
										{
											$arguments = $argumentArray[1];
										}
								}

							if (!isset($arguments))
								{
									$arguments = '';
								}

							switch ($command)
								{
									case 'exit':
										$application = false;
										break;
									case 'quit':
										$application = false;
										break;
									default:
										$this->handleCommand($command, $arguments, $argumentArray);
								}

							unset($arguments);
							unset($argumentArray);
							unset($inputArray);
							unset($input);
							unset($command);

							$this->postCommandHook();
						}
				}

			public function runCommand($command)
				{

				}

			public function handleCommand($command, $arguments, $argumentArray)
				{
					/*
					 * If command starts with dollar sign, let's just evaluate php.
					 */
					if (strlen($command) == 0)
						{
							$this->printLine('',false);
						}
					elseif ($command[0] == '$')
						{
							$split = str_split($command);
							$eval = '';
							foreach($split as $index => $val)
								{
									if ($index > 0)
										{
											$eval .= $val;
										}
								}

							$eval = eval($eval . ' ' . $arguments);
							$this->printLine($eval);
						}
					elseif ($command == 'eval')
						{
							$this->printLine(eval ($arguments));
						}

					elseif ($command == 'prompt')
						{
							$this->setPrompt($arguments);
						}

					elseif ($command == 'clear')
						{
							for ($i = 0; $i <= 100; $i++)
								{
									$this->printLine('');
								}
						}

					elseif ($command == 'exec')
						{
							$exec = passthru($arguments);
							$this->printLine($exec);
						}

					elseif ($command == 'echo')
						{
							$this->printLine($arguments);
						}

					elseif ($command == 'help' or $command == '?')
						{
							$this->printLine('');
							$this->printLine('########### [AVAILABLE COMMANDS] ###########');
							$this->printLine('exit - exits shell');
							$this->printLine('quit - exits shell');
							$this->printLine('eval [$args] - evaluates arguments as php code');
							$this->printLine('$[$args] - shorthand for eval. Example - \'$echo 123\' = \'eval echo 123;\'');
							$this->printLine('prompt [$prompt] - changes prompt to $prompt');
							$this->printLine('clear - clears screen');
							$this->printLine('exec - executes shell command');
							$this->printLine('math - do simple calculations');
							$this->printLine('');
						}

					elseif ($command == 'math')
						{
							$result = eval("return $arguments;");
							$this->printLine($result);
						}
					else
						{
							$this->printLine("Unknown command: $command. Type \"help\" for more info.");
						}
				}

			public function setPrompt($prompt)
				{
					$this->prompt = $prompt . ' ';
				}

			public function getPrompt()
				{
					return $this->prompt;
				}

			public function getInput()
				{
					$stdin = fopen("php://stdin", 'r');
					$input = fgets($stdin, 1024);
					$input = trim($input);
					fclose($stdin);
					return $input;
				}

			public function postCommandHook()
				{
					//$this->setPrompt(date('d-m-Y H:i:s'));
					//TODO: implement post command hooks, so prompt could be dynamic (lol, time), and things like that.
				}
		}