<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Archive_logs_model extends MY_Model
{
	function __construct()
	{
		parent::__construct();

		$this->table = 'archive_logs';
		$this->primaryKey = 'archive_log_id';
		$this->returnArray = FALSE;

		$this->validation_rules = array(
			array('field' => 'archive_log_id', 'label' => 'Archive Log Id', 'rules' => 'trim')
			,array('field' => 'import_date', 'label' => 'Import Date', 'rules' => 'trim|required')
			,array('field' => 'import_cnt', 'label' => 'Import Cnt', 'rules' => 'trim|required')
		);

		$this->fields = array(
			'archive_log_id'
			,'import_date'
			,'import_cnt'
		);
	}
}

/* End of file Archive_logs_model.php */
/* Location: ./application/models/Archive_logs_model.php */
