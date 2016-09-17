<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Alex_archives_model extends MY_Model
{
	function __construct()
	{
		parent::__construct();

		$this->table = 'alex_archives';
		$this->primaryKey = 'archive_id';
		$this->returnArray = FALSE;

		$this->validation_rules = array(
			array('field' => 'archive_id', 'label' => 'Archive Id', 'rules' => 'trim')
			,array('field' => 'event_id', 'label' => 'Event Id', 'rules' => 'trim')
			,array('field' => 'station_id', 'label' => 'Station Id', 'rules' => 'trim')
			,array('field' => 'image', 'label' => 'Image', 'rules' => 'trim|required')
			,array('field' => 'image_folder', 'label' => 'Image Folder', 'rules' => 'trim|required')
			,array('field' => 'created_at', 'label' => 'Created At', 'rules' => 'trim|required')
			,array('field' => 'created_at_ms', 'label' => 'Created At Ms', 'rules' => 'trim|required')
			,array('field' => 'maxMag', 'label' => 'MaxMag', 'rules' => 'trim|required')
		);

		$this->fields = array(
			'archive_id'
			,'event_id'
			,'station_id'
			,'image'
			,'image_folder'
			,'created_at'
			,'created_at_ms'
			,'maxMag'
		);
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

		$this->db->join('archive_events', 'event_id', 'LEFT');
		$this->db->select('event_name, event_slug, event_code');

		$this->db->join('archive_stations', 'station_id', 'LEFT');
		$this->db->select('station_name, station_slug, station_code');


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

		$this->db->join('archive_events', 'event_id', 'LEFT');
		$this->db->select('event_name, event_slug, event_code');

		$this->db->join('archive_stations', 'station_id', 'LEFT');
		$this->db->select('station_name, station_slug, station_code');

		$query = $this->db->get($this->table);
		$this->numRows = $query->num_rows();

		return ($this->returnArray) ? $query->result_array() : $query->result();
	}


	function find_stations_count($conditions = NULL)
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

   	$this->db->select('COUNT(DISTINCT station_id) AS count');
		$this->db->limit(1);

		$this->db->join('archive_events', 'event_id', 'LEFT');
		$this->db->select('event_name, event_slug, event_code');

		$this->db->join('archive_stations', 'station_id', 'LEFT');
		$this->db->select('station_name, station_slug, station_code');

		$query = $this->db->get($this->table);
		$this->numRows = $query->num_rows();

		return (int) $query->row()->count;
	}

	function stats($conditions = NULL, $fields = '*', $order = NULL, $start = 0, $limit = NULL, $calc_found_rows = FALSE)
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

		$this->db->join('archive_events', 'event_id', 'LEFT');
		$this->db->select('event_name, event_slug, event_code');

      $this->db->select('COUNT(DISTINCT station_id) AS stations_count');
      $this->db->select('COUNT(*) AS archives_count');

      $this->db->group_by('event_id');
		//$this->db->join('archive_stations', 'station_id', 'LEFT');
		//$this->db->select('station_name, station_slug, station_code');

		$query = $this->db->get($this->table);
		$this->numRows = $query->num_rows();

		return ($this->returnArray) ? $query->result_array() : $query->result();
	}

}

/* End of file Archives_model.php */
/* Location: ./application/models/Archives_model.php */
