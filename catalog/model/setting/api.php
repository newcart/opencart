<?php
class ModelSettingApi extends Model {

	protected $table = 'api';
	protected $primaryKey = 'api_id';
	
	public function login($username, $password) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "api WHERE username = '" . $this->db->escape($username) . "' AND password = '" . $this->db->escape($password) . "'");

		return $query->row;
	}
}