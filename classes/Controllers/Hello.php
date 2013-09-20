<?php

namespace Controllers;

class Hello extends Base {
	public function getIndex() {

		$this->view->render('world');
	}

	public function info() {
		$this->view->render('info');
	}
}