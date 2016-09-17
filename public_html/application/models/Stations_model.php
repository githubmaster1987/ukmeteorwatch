<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Stations_model extends MY_Model
{
	function __construct()
	{
		parent::__construct();

		$this->table = 'stations';
		$this->primaryKey = 'station_id';
		$this->returnArray = FALSE;

		$this->validation_rules = array(
			array('field' => 'station_id', 'label' => 'Station Id', 'rules' => 'trim')
			,array('field' => 'station_folder', 'label' => 'Station Folder', 'rules' => 'trim|required')
			,array('field' => 'station_slug', 'label' => 'Station Slug', 'rules' => 'trim|required')
			,array('field' => 'station_name', 'label' => 'Station Name', 'rules' => 'trim|required')
			,array('field' => 'is_station_active', 'label' => 'Is Station Active', 'rules' => 'trim|required')
		);

		$this->fields = array(
			'station_id'
			,'station_folder'
			,'station_slug'
			,'station_name'
			,'is_station_active'
		);
	}
}

/* End of file Stations_model.php */
/* Location: ./application/models/Stations_model.php */
