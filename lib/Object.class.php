<?php
	class Object
			{
				function __call($f, $x)
					{
						return call_user_func_array($this->$f, $x);
					}
			}