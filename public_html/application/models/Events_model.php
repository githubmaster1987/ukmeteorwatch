<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Events_model extends MY_Model
{
	function __construct()
	{
		parent::__construct();

		$this->table = 'events';
		$this->primaryKey = 'event_id';
		$this->returnArray = FALSE;

		$this->validation_rules = array(
			array('field' => 'event_id', 'label' => 'Event Id', 'rules' => 'trim')
			,array('field' => 'event_folder', 'label' => 'Event Folder', 'rules' => 'trim|required')
			,array('field' => 'event_slug', 'label' => 'Event Slug', 'rules' => 'trim|required')
			,array('field' => 'event_name', 'label' => 'Event Name', 'rules' => 'trim|required')
			,array('field' => 'is_event_active', 'label' => 'Is Event Active', 'rules' => 'trim|required')
		);

		$this->fields = array(
			'event_id'
			,'event_folder'
			,'event_slug'
			,'event_name'
			,'is_event_active'
		);
	}
}

/* End of file Events_model.php */
/* Location: ./application/models/Events_model.php */
