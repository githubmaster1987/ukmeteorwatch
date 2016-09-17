<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Archive_stations_model extends MY_Model
{
	function __construct()
	{
		parent::__construct();

		$this->table = 'archive_stations';
		$this->primaryKey = 'archive_station_id';
		$this->returnArray = FALSE;

		$this->validation_rules = array(
			array('field' => 'station_id', 'label' => 'Station Id', 'rules' => 'trim')
			,array('field' => 'station_code', 'label' => 'Station Code', 'rules' => 'trim|required')
			,array('field' => 'station_slug', 'label' => 'Station Slug', 'rules' => 'trim|required')
			,array('field' => 'station_name', 'label' => 'Station Name', 'rules' => 'trim|required')
		);

		$this->fields = array(
			'station_id'
			,'station_code'
			,'station_slug'
			,'station_name'
		);
	}
}

/* End of file Archive_stations_model.php */
/* Location: ./application/models/Archive_stations_model.php */
