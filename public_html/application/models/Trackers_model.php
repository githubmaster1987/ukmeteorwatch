<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Trackers_model extends MY_Model
{
	function __construct()
	{
		parent::__construct();

		$this->table = 'trackers';
		$this->primaryKey = 'tracker_id';
		$this->returnArray = FALSE;

		$this->validation_rules = array(
			array('field' => 'tracker_id', 'label' => 'User Tracker Id', 'rules' => 'trim')
			,array('field' => 'ip_address', 'label' => 'Ip Address', 'rules' => 'trim|required')
			,array('field' => 'domain', 'label' => 'Domain', 'rules' => 'trim|required')
			,array('field' => 'last_failure', 'label' => 'Last Failure', 'rules' => 'trim|required')
			,array('field' => 'failed_logins', 'label' => 'Failed Logins', 'rules' => 'trim|required')
			,array('field' => 'banned', 'label' => 'Banned', 'rules' => 'trim|required')
			,array('field' => 'access_count', 'label' => 'Access Count', 'rules' => 'trim|required')
		);

		$this->fields = array(
			'tracker_id'
			,'ip_address'
			,'domain'
			,'last_failure'
			,'failed_logins'
			,'banned'
			,'access_count'
		);
	}

	// --------------------------------------------------------------------

	/**
	 * Add IP
	 *
	 * Add an ip address to the tracker
	 *
	 * @access	private
	 * @return	mixed	new row
	 */
	function add_ip()
	{
		$ip = $this->input->ip_address();
		$query = $this->db->where('ip_address',$ip)->limit(1)->get($this->table);

		if($query->num_rows > 0)
		{
			return $query->row();
		}
		$this->db->set('ip_address', $ip);
		$this->db->set('domain', get_host($ip));
		$this->db->insert($this->table);

		$this->db->where('tracker_id', $this->db->insert_id());
		$query = $this->db->get($this->table);

		return $query->row();

	}

	// --------------------------------------------------------------------

	/**
	 * Increment failure count and set last login time
	 *
	 * @access	public
	 * @return	object	user object
	 */
	function increment_failures($failed_so_far)
	{
		$now = time();
		$this->db->where('ip_address', $this->input->ip_address());

		if($failed_so_far < 4) // Not relevant beyond this point
		{
			$this->db->set('failed_logins', 'failed_logins + 1', FALSE);
		}

		$this->db->set('last_failure', $now);
		$this->db->update($this->table);
	}
	// --------------------------------------------------------------------

	/**
	 * Increment failure count and set last login time
	 *
	 * @access	public
	 * @return	object	user object
	 */
	function increment_access()
	{
		$this->db->where('ip_address', $this->input->ip_address());
		$this->db->set('access_count', 'access_count + 1', FALSE);
		$this->db->update($this->table);
	}

	// --------------------------------------------------------------------

	/**
	 * Resets login failure count
	 *
	 * @access	public
	 * @return	object	user object
	 */
	function reset_failures()
	{
		$this->db->where('ip_address', $this->input->ip_address());
		$this->db->set('failed_logins', 0);

		$this->db->update($this->table);
	}

}
