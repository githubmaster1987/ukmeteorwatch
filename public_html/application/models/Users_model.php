<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Users_model extends MY_Model
{
	function __construct()
	{
		parent::__construct();

		$this->table = 'users';
		$this->primaryKey = 'user_id';
		$this->returnArray = FALSE;

		$this->validation_rules = array(
			array('field' => 'user_id', 'label' => 'User Id', 'rules' => 'trim')
			,array('field' => 'unique_id', 'label' => 'Unique Id', 'rules' => 'trim')
			,array('field' => 'user_group_id', 'label' => 'User Group Id', 'rules' => 'trim')
			,array('field' => 'tracker_id', 'label' => 'Tracker Id', 'rules' => 'trim')
			,array('field' => 'email', 'label' => 'Email', 'rules' => 'trim|required')
			,array('field' => 'password', 'label' => 'Password', 'rules' => 'trim|required')
			,array('field' => 'hash', 'label' => 'Hash', 'rules' => 'trim')
			,array('field' => 'active', 'label' => 'Active', 'rules' => 'trim')
			,array('field' => 'rem_token', 'label' => 'Rem Token', 'rules' => 'trim')
			,array('field' => 'rem_timeout', 'label' => 'Rem Timeout', 'rules' => 'trim')
			,array('field' => 'last_visit', 'label' => 'Last Visit', 'rules' => 'trim')
			,array('field' => 'login_count', 'label' => 'Login Count', 'rules' => 'trim')
			,array('field' => 'activation_code', 'label' => 'Activation Code', 'rules' => 'trim')
			,array('field' => 'dob', 'label' => 'Dob', 'rules' => 'trim|required')
			,array('field' => 'first_name', 'label' => 'Name', 'rules' => 'trim')
			,array('field' => 'last_name', 'label' => 'Name', 'rules' => 'trim')
			,array('field' => 'description', 'label' => 'description', 'rules' => 'trim')
			,array('field' => 'company_id', 'label' => 'Company Id', 'rules' => 'trim')
		);

		$this->fields = array(
			'user_id'
			,'unique_id'
			,'user_group_id'
			,'tracker_id'
			,'email'
			,'password'
			,'hash'
			,'rem_token'
			,'rem_timeout'
			,'created_at'
			,'updated_at'
			,'last_visit'
			,'login_count'
			,'activation_code'
			,'first_name'
			,'last_name'
			,'active'
		);
    }


	function update_remember($unique_id, $token, $timeout)
	{
		$data = array(
			'rem_token'		=>	$token,
			'rem_timeout'	=> time() + $timeout
		);

		$this->db->where('unique_id', $unique_id);
		$this->db->update($this->table, $data);
	}

	// --------------------------------------------------------------------

	function login_update($user_id, $tracker_id)
	{
		$this->db->set('tracker_id', $tracker_id);
		$this->db->set('login_count', 'login_count+1', FALSE);

		$this->db->where('user_id', $user_id);
		$this->db->update($this->table);
	}

}

/* End of file users_model.php */
/* Location: ./application/models/users_model.php */
