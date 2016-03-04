<?php
class ModelCatalogUrlAlias extends Model {

	protected $table = 'url_alias';
	protected $primaryKey = 'url_alias_id';

	public function getUrlAlias($keyword) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "url_alias WHERE keyword = '" . $this->db->escape($keyword) . "'");

		return $query->row;
	}
}