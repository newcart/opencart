<?php
class ModelLocalisationLocation extends Model {

	protected $table = 'location';
	protected $primaryKey = 'location_id';
	
	public function getLocation($location_id) {
		$query = $this->db->query("SELECT location_id, name, address, geocode, telephone, fax, image, open, comment FROM " . DB_PREFIX . "location WHERE location_id = '" . (int)$location_id . "'");

		return $query->row;
	}
}