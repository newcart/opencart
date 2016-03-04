<?php
use Illuminate\Database\Eloquent\Model as EloquentModel;

abstract class Model extends EloquentModel{
	protected $registry;

	public $timestamps = false;
//	protected $table = 'table';
//	protected $primaryKey = 'table_id';

	public function __construct() {

		global $registry;
		$this->registry = $registry;
	}

	public function __get($key) {
		return $this->registry->get($key);
	}

	public function __set($key, $value) {
		$this->registry->set($key, $value);
	}
}