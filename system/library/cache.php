<?php
class Cache {
	private $cache;

	public function __construct($driver, $expire = 3600) {
		if($this->checkEnableCache()) {
			$class = 'Cache\\' . $driver;

			if (class_exists($class)) {
				$this->cache = new $class($expire);
			} else {
				exit('Error: Could not load cache driver ' . $driver . ' cache!');
			}
		}
	}

	public function get($key) {
        if(!$this->checkEnableCache()) {
            return false;
        }

		return $this->cache->get($key);
	}

	public function set($key, $value) {
        if(!$this->checkEnableCache()) {
            return false;
        }

        return $this->cache->set($key, $value);
	}

	public function delete($key) {
        if(!$this->checkEnableCache()) {
            return false;
        }

        return $this->cache->delete($key);
	}

	private function checkEnableCache() {
        global $registry;

        return (boolean)$registry->get('config')->get('enable_cache');
	}
}
