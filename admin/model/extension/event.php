<?php
class ModelExtensionEvent extends Model {

	protected $table = 'event';
	protected $primaryKey = 'event_id';

	public function addEvent($code, $trigger, $action) {
		$this->db->query("INSERT INTO " . DB_PREFIX . "event SET `code` = '" . $this->db->escape($code) . "', `trigger` = '" . $this->db->escape($trigger) . "', `action` = '" . $this->db->escape($action) . "'");
	}

	public function deleteEvent($code) {
		$this->db->query("DELETE FROM " . DB_PREFIX . "event WHERE `code` = '" . $this->db->escape($code) . "'");
	}
}