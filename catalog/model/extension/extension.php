<?php
class ModelExtensionExtension extends Model {

	protected $table = 'extension';
	protected $primaryKey = 'extension_id';
	
	function getExtensions($type) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "extension WHERE `type` = '" . $this->db->escape($type) . "'");

		return $query->rows;
	}
}