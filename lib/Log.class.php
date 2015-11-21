<?php

	class Log extends Core
		{

			public function __construct()
				{
					$this->time = date('d-m-Y H:i:s');
				}

			public function logMessage($fileName, $message, $additional = false, $level = false)
				{
					if ($level)
						{
							$level = strtolower($level);
							if ($level == 'info' or $level == 'warn' or $level == 'error' or $level == 'fatal' or $level == 'notice')
								{
									$level = strtoupper($level);
								}
							else
								{
									$level = '-';
								}
						}
					else
						{
							$level = '-';
						}

					$lowerLevel = strtolower($level);
					$ip = $this->ip();

					$rawFile = '../logs/raw/'.$fileName.'.log';
					$htmlFile = '../logs/html/'.$fileName.'.html';
					$rawLog = "[$this->time] [$ip] [$level]: $message ($additional)\r\n";
					$htmlLog = "<p class=\"logMessage $lowerLevel\"><span class=\"time\">[$this->time]</span> <span class=\"ip-address\">[$ip]</span> <span class=\"level $lowerLevel\">[$level]</span>: <span class=\"message\">$message</span> <span class=\"additional\">($additional)</span></p> \r\n";
					file_put_contents($rawFile, $rawLog, FILE_APPEND | LOCK_EX);
					file_put_contents($htmlFile, $htmlLog, FILE_APPEND | LOCK_EX);
				}

			public function configLog($node, $type, $message, $lineNote = false)
				{
					$file = '../logs/raw/config.log';
					$message = "[$this->time] [$this->ip()] [$node] [$type] : $message ($lineNote) \r\n";
					file_put_contents($file, $message, FILE_APPEND | LOCK_EX);
				}

			public function newEntry($module, $type, $message)
				{
					$module = strtoupper($module);
					$type = strtoupper($type);

					$message = "[$this->time] [$this->ip()] [$module] [$type]: $message\r\n";
					$file = '../logs/raw/'.$module.'.log';

					file_put_contents($file, $message, FILE_APPEND | LOCK_EX);

				}

		}