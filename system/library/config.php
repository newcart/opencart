<?php
class Config {
	private $data = array();

	public function get($key) {
		return (isset($this->data[$key]) ? $this->data[$key] : null);
	}

	public function set($key, $value) {
		$this->data[$key] = $value;
	}

	public function has($key) {
		return isset($this->data[$key]);
	}

	public function load($filename) {
		$file = DIR_CONFIG . $filename . '.php';

		if (file_exists($file)) {
			$_ = array();

			require(Vqmod::modCheck($file));

			$this->data = array_merge($this->data, $_);
		} else {

			//load extension config
			$file = DIR_EXTENSIONS . '*/app/system/config/' . $filename . '.php';
			$file_extensions = glob($file);

			if (isset($file_extensions[0]) && file_exists($file_extensions[0])) {
				$_ = array();

				require(Vqmod::modCheck($file_extensions[0]));

				$this->data = array_merge($this->data, $_);
			} else {
				trigger_error('Error: Could not load config ' . $filename . '!');
				exit();
			}
		}
	}
}