<?php

	class Date extends DateTime
		{
			public function __construct($date = false)
				{
					if (!$date)
						{
							$this->timestamp = time();
							$this->string = $this->convert('d-m-Y h:i:s');
						}
					else
						{
							$this->timestamp = strtotime($date);
							$this->string = $date;
						}
				}

			public function __toString()
				{
					return $this->string;
				}

			public function convert($to)
				{
					$this->string = date($to, $this->timestamp);
					return date($to, $this->timestamp);
				}
		}