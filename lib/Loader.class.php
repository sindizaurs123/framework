<?php


	class Loader extends Core
	{

		public function __construct()
		{
			$this->route = array();
			$this->redirects = array();
			$this->error = false;
			$this->action404 = false;
			$this->errorActions = array();
		}

		public function addRouteItem($param, $path, $is_redirect = false, $href = '/')
		{
			if ($is_redirect == false) {
				$this->route[$param] = $path;
			} else {
				$this->redirects[$param] = $href;
			}
		}

		public function destroyRouteItem($param)
		{
			unset($this->route[$param]);
		}

		public function destroyRedirect($param)
		{
			unset($this->redirect[$param]);
		}

		public function getRouteItem($param)
		{
			if (isset($this->route[$param])) {
				return $this->route[$param];
			} else {
				return false;
			}
		}

		public function getRedirect($param)
		{
			if (isset($this->redirects[$param])) {
				return $this->redirects[$param];
			} else {
				return false;
			}
		}
		public function route()
		{
			$param = $this->url(1);

			if ($this->getRouteItem($param)) {
				$pathName = $this->getRouteItem($param);

				if (!$this->modules()->moduleExists($pathName)) {
					// Module does not exist, do something
					$this->log()->logMessage('class.Loader', 'Attempted to load non-existent module (' . $pathName . ')', 'class.Loader.php line 61', 'error');
					header('Location: /');
				}

				if (!$this->modules()->isLoaded($pathName)) {
					$this->modules()->getModule($pathName, 'route');
				} else {
					$this->modules()->getModule($pathName)->__construct('route');
				}


			} elseif ($this->getRedirect($param)) {
				header('Location: ' . $this->getRedirect($param));
			} else {
				$this->error = 404;
				$this->log()->logMessage('class.Loader', 'User encountered 404 error.', 'Request URI: ' . $this->url() . '', 'info');
			}


		}

		public function getError()
		{
			$param = $this->url(1);
			if (!$this->getRouteItem($param) && !$this->getRedirect($param)) {
				$this->error = 404;
			}

			return $this->error;
		}

		public function setErrorAction($errorCode, $action) {
			$this->errorActions[$errorCode] = $action;

			return $this;
		}

		public function getErrorAction($errorCode) {

			if (isset($this->errorActions[$errorCode])) {
				return $this->errorActions[$errorCode];
			} else {
				return false;
			}

		}

		public function executeErrorAction($errorCode = null) {

			if (!$errorCode) {
				$errorCode = $this->error;
			}

			$errorAction = $this->getErrorAction($errorCode);

			if ($errorAction) {
				eval($errorAction.";");
			}
		}

		public function get404action()
		{
			return $this->getErrorAction(404);
		}

		public function set404action($action)
		{
			$this->setErrorAction(404, $action);

			return true;
		}
	}