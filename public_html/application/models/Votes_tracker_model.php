<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Votes_tracker_model extends MY_Model
{
	function __construct()
	{
		parent::__construct();

		$this->table = 'votes_tracker';
		$this->primaryKey = 'vote_id';
		$this->returnArray = FALSE;

		$this->validation_rules = array(
			array('field' => 'vote_id', 'label' => 'Vote Id', 'rules' => 'trim')
			,array('field' => 'ip_address', 'label' => 'Ip Address', 'rules' => 'trim|required')
			,array('field' => 'voted_at', 'label' => 'Voted At', 'rules' => 'trim|required')
		);

		$this->fields = array(
			'vote_id'
			,'ip_address'
			,'voted_at'
		);
	}
}

/* End of file Votes_tracker_model.php */
/* Location: ./application/models/Votes_tracker_model.php */
