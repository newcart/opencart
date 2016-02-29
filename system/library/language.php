<?php

use \Newcart\System\Modification\System\Library\Language as NewcartLanguage;

class Language {
	private $default = 'english';
	private $directory;
	private $data = array();

	public function __construct($directory = '') {
		$this->directory = $directory;
		$this->default = (new NewcartLanguage())->getDefault();
	}

	public function get($key) {
		return (isset($this->data[$key]) ? $this->data[$key] : $key);
	}

	public function all() {
		return $this->data;
	}

	public function load($filename) {
		$_ = array();

		$file = DIR_LANGUAGE . $this->default . '/' . $filename . '.php';

		if (file_exists($file)) {
			require($file);
		}

		$file = DIR_LANGUAGE . $this->directory . '/' . $filename . '.php';

		if (file_exists($file)) {
			require($file);
		}

		(new NewcartLanguage())->load($filename, $this->directory, $this->default);

		$this->data = array_merge($this->data, $_);

		return $this->data;
	}
}