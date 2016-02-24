<?php
class Language {
	private $default = 'english';
	private $directory;
	private $data = array();

	public function __construct($directory = '') {
		global $registry;
		$this->directory = $directory;
		$default = $registry->get('config')->get('default_language');
		$this->default = $default ? $default : 'english';
	}

	public function get($key) {
		return (isset($this->data[$key]) ? $this->data[$key] : $key);
	}

	public function all() {
		return $this->data;
	}

	public function load($filename) {
		$_ = array();

		global $registry;

		$file = DIR_LANGUAGE . $this->default . '/' . $filename . '.php';

		if (file_exists($file)) {
			require($file);
		}

		$file = DIR_LANGUAGE . $this->directory . '/' . $filename . '.php';

		if (file_exists($file)) {
			require($file);
		}

		//load extension language
		$file = DIR_ROOT . '/' . $registry->get('config')->get('extension_path') . '/*/*/' . $registry->get('config')->get('environment') . '/language/' . $this->directory . '/' . $filename . '.php';
		$file_extensions = glob($file);

		foreach ($file_extensions as $file_extension) {
			if (file_exists($file_extension)) {
				require($file_extension);
			}
		}

		//load extension language default
		$file = DIR_ROOT . '/' . $registry->get('config')->get('extension_path') . '/*/*/' . $registry->get('config')->get('environment') . '/language/' . $this->default . '/' . $filename . '.php';
		$file_extensions = glob($file);

		foreach ($file_extensions as $file_extension) {
			if (file_exists($file_extension)) {
				require(VQMod::modCheck($file_extension));
			}
		}

		//load theme language
		$file_theme = DIR_TEMPLATE . $registry->get('config')->get('config_template') . '/language/' . $this->default . '/' . $filename . '.php';

		if (file_exists($file_theme)) {
			require_once($file_theme);
		}

		$file_theme = DIR_TEMPLATE . $registry->get('config')->get('config_template') . '/language/' . $this->directory . '/' . $filename . '.php';

		if (file_exists($file_theme)) {
			require_once($file_theme);
		}

		$this->data = array_merge($this->data, $_);

		return $this->data;
	}
}