<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Votes_model extends MY_Model
{
	function __construct()
	{
		parent::__construct();

		$this->table = 'votes';
		$this->primaryKey = 'vote_id';
		$this->returnArray = FALSE;

		$this->validation_rules = array(
			array('field' => 'vote_id', 'label' => 'Vote Id', 'rules' => 'trim')
			,array('field' => 'meteor_id', 'label' => 'Meteor Id', 'rules' => 'trim')
			,array('field' => 'votes_cnt', 'label' => 'Votes Cnt', 'rules' => 'trim|required')
		);

		$this->fields = array(
			'vote_id'
			,'meteor_id'
			,'votes_cnt'
		);
	}
}

/* End of file Votes_model.php */
/* Location: ./application/models/Votes_model.php */
