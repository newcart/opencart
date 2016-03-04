<?php
class ModelCheckoutMarketing extends Model {

	protected $table = 'marketing';
	protected $primaryKey = 'marketing_id';
	
	public function getMarketingByCode($code) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "marketing WHERE code = '" . $this->db->escape($code) . "'");

		return $query->row;
	}
}