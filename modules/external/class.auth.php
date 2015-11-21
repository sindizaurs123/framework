<?php

/*
 * Lietotaja autorizācijas klase
 *
 * (c) 2012, Maadinsh, http://coding.lv/
 *
 * Izmanto pēc saviem ieskatiem :)
 *
 */

class Auth {

	var $salt = 'TwTnKpp|wdIzV@g6JFaoV!oZ&egarhOo';

	function Auth($db) {
		$this->id = 0;
		$this->error = 0;
		$this->db = $db;
		$this->ip = $_SERVER['REMOTE_ADDR'];
		$this->check_session();
	}

	function check_session() {

		if (!empty($_SESSION['auth_id'])) {
			$userdata = $this->db->get_row("SELECT * FROM `users` WHERE `id` = '" . intval($_SESSION['auth_id']) . "'");
			if ($userdata) {
				foreach ($userdata as $field => $value) {
					$this->$field = $value;
				}
				$this->db->query("UPDATE `users` SET `accessed` = NOW() WHERE `id` = $this->id");
				return true;
			} else {
				$this->logout();
				return false;
			}
		}
		return false;
	}

	function login($username, $password) {

		$pwd = $this->pwd($password);
		$login = $this->db->real_escape_string($username);
		$userdata = $this->db->get_row("SELECT * FROM `users` WHERE `username` = '" . $login . "' AND `password` = '$pwd' LIMIT 1");

		if ($userdata) {
			foreach ($userdata as $field => $value) {
				$this->$field = $value;
			}
			$_SESSION['auth_id'] = $this->id;
			$this->db->query("UPDATE `users` SET `accessed` = NOW() WHERE `id` = $this->id");
			return true;
		}

		sleep(1);
		$this->error = 1;
		return false;
	}

	function logout() {
		$this->id = 0;
		$_SESSION['auth_id'] = 0;
		session_destroy();
	}

	function pwd($pwd) {
		return hash('sha256', $pwd . $this->salt);
	}

}
