<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Meteors_model extends MY_Model
{
	function __construct()
	{
		parent::__construct();

		$this->table = 'meteors';
		$this->primaryKey = 'meteor_id';
		$this->returnArray = FALSE;

		$this->validation_rules = array(
			array('field' => 'meteor_id', 'label' => 'Meteor Id', 'rules' => 'trim')
			,array('field' => 'event_id', 'label' => 'Event Id', 'rules' => 'trim')
			,array('field' => 'station_id', 'label' => 'Station Id', 'rules' => 'trim')
			,array('field' => 'image', 'label' => 'Image', 'rules' => 'trim|required')
			,array('field' => 'created_at', 'label' => 'Created At', 'rules' => 'trim|required')
			,array('field' => 'status', 'label' => 'Status', 'rules' => 'trim|required')
		);

		$this->fields = array(
			'meteor_id'
			,'event_id'
			,'station_id'
			,'image'
			,'created_at'
			,'status'
		);
	}

	function get_live($conditions = NULL, $fields = '*', $order = NULL, $start = 0, $limit = NULL)
	{
		if ($conditions != NULL)
		{
			if (is_array($conditions))
			{
				$this->db->where($conditions);
			}
			else
			{
				$this->db->where($conditions, NULL, FALSE);
			}
		}

		if ($fields == '*' OR $fields == NULL)
		{
			$this->db->select('SQL_CALC_FOUND_ROWS `' . $this->db->dbprefix($this->table) . "`.*", FALSE);
		}
		else
		{
			$this->db->select('SQL_CALC_FOUND_ROWS ' . $fields, FALSE);
		}

		if ($order != NULL)
		{
			$this->db->order_by($order);
		}

		if ($limit != NULL)
		{
			$this->db->limit($limit, $start);
		}


		$this->db->join('events', 'event_id', 'LEFT');
		$this->db->select('event_name, event_slug, event_folder');

		$this->db->join('stations', 'station_id', 'LEFT');
		$this->db->select('station_name, station_slug, station_folder');

		$this->db->join('votes', 'meteor_id', 'LEFT');
		$this->db->select('vote_id, votes_cnt');

//		$this->db->where('meteors.created_at = (SELECT MAX(created_at) FROM '.$this->table.' AS m WHERE m.event_id = meteors.event_id AND m.station_id = meteors.station_id ) ', NULL, FALSE);

//		$this->db->group_by('event_id, station_id');

		$query = $this->db->get($this->table);

		$this->numRows = $query->num_rows();

		return ($this->returnArray) ? $query->result_array() : $query->result();
	}

	function get_latest($conditions = NULL, $fields = '*', $order = NULL, $start = 0, $limit = NULL)
	{
		if ($conditions != NULL)
		{
			if (is_array($conditions))
			{
				$this->db->where($conditions);
			}
			else
			{
				$this->db->where($conditions, NULL, FALSE);
			}
		}

		if ($fields == '*' OR $fields == NULL)
		{
			$this->db->select('SQL_CALC_FOUND_ROWS `' . $this->db->dbprefix($this->table) . "`.*", FALSE);
		}
		else
		{
			$this->db->select('SQL_CALC_FOUND_ROWS ' . $fields, FALSE);
		}

		if ($order != NULL)
		{
			$this->db->order_by($order);
		}

		if ($limit != NULL)
		{
			$this->db->limit($limit, $start);
		}

		$this->db->join('events', 'event_id', 'LEFT');
		$this->db->select('event_name, event_slug, event_folder');

		$this->db->join('stations', 'station_id', 'LEFT');
		$this->db->select('station_name, station_slug, station_folder');

		$this->db->join('votes', 'meteor_id', 'LEFT');
		$this->db->select('vote_id, votes_cnt');


		$query = $this->db->get($this->table);

		$this->numRows = $query->num_rows();

		return ($this->returnArray) ? $query->result_array() : $query->result();
	}

	function findAll($conditions = NULL, $fields = '*', $order = NULL, $start = 0, $limit = NULL, $calc_found_rows = FALSE)
	{
		if ($conditions != NULL)
		{
			if (is_array($conditions))
			{
				$this->db->where($conditions);
			}
			else
			{
				$this->db->where($conditions, NULL, FALSE);
			}
		}

		if ($fields == '*' OR $fields == NULL)
		{
			if ($calc_found_rows)
			{
				$this->db->select('SQL_CALC_FOUND_ROWS `' . $this->db->dbprefix($this->table) . "`.*", FALSE);
			}
			else
			{
				$this->db->select($this->table . '.*');
			}
		}
		else
		{
			if ($calc_found_rows)
			{
				$this->db->select('SQL_CALC_FOUND_ROWS ' . $fields, FALSE);
			}
			elseif(stripos($fields, 'DISTINCT') !== FALSE)
			{
				$this->db->select($fields, FALSE);
			}
			else
			{
				$this->db->select($fields);
			}
		}

		if ($order != NULL)
		{
			$this->db->order_by($order);
		}

		if ($limit != NULL)
		{
			$this->db->limit($limit, $start);
		}

		$this->db->join('events', 'event_id', 'LEFT');
		$this->db->select('event_name, event_slug, event_folder');

		$this->db->join('stations', 'station_id', 'LEFT');
		$this->db->select('station_name, station_slug, station_folder');

		$this->db->join('votes', 'meteor_id', 'LEFT');
		$this->db->select('vote_id, votes_cnt');

		$query = $this->db->get($this->table);
		$this->numRows = $query->num_rows();

		return ($this->returnArray) ? $query->result_array() : $query->result();
	}

	function getPaginated($conditions = NULL, $fields = '*', $order = NULL, $start = 0, $limit = NULL)
	{
		if ($conditions != NULL)
		{
			if (is_array($conditions))
			{
				$this->db->where($conditions);
			}
			else
			{
				$this->db->where($conditions, NULL, FALSE);
			}
		}

		if ($fields == '*' OR $fields == NULL)
		{
			$this->db->select('SQL_CALC_FOUND_ROWS `' . $this->db->dbprefix($this->table) . "`.*", FALSE);
		}
		else
		{
			$this->db->select('SQL_CALC_FOUND_ROWS ' . $fields, FALSE);
		}

		if ($order != NULL)
		{
			$this->db->order_by($order);
		}

		if ($limit != NULL)
		{
			$this->db->limit($limit, $start);
		}

		$this->db->join('events', 'event_id', 'LEFT');
		$this->db->select('event_name, event_slug, event_folder');

		$this->db->join('stations', 'station_id', 'LEFT');
		$this->db->select('station_name, station_slug, station_folder');

		$this->db->join('votes', 'meteor_id', 'LEFT');
		$this->db->select('vote_id, votes_cnt');

		$query = $this->db->get($this->table);

		$this->numRows = $query->num_rows();

		return ($this->returnArray) ? $query->result_array() : $query->result();
	}

}

/* End of file Meteors_model.php */
/* Location: ./application/models/Meteors_model.php */
