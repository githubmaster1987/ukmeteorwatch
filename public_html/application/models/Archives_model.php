<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Archives_model extends MY_Model
{
	function __construct()
	{
		parent::__construct();

		$this->table = 'archives';
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
			,array('field' => 'mag', 'label' => 'Mag', 'rules' => 'trim')
			,array('field' => 'cam', 'label' => 'Camera name', 'rules' => 'trim')
			,array('field' => 'lens', 'label' => 'Lens name', 'rules' => 'trim')
			,array('field' => 'rstar', 'label' => 'Number of reference stars', 'rules' => 'trim')
			,array('field' => 'sec', 'label' => 'Duration', 'rules' => 'trim')
			,array('field' => 'av', 'label' => 'Angular velocity at ram, dcm', 'rules' => 'trim')
			,array('field' => 'vo', 'label' => 'Calculated velocity in km/s', 'rules' => 'trim')
			,array('field' => 'h1', 'label' => 'Initial height in km', 'rules' => 'trim')
			,array('field' => 'h2', 'label' => 'Terminal height in km', 'rules' => 'trim')
			,array('field' => 'len', 'label' => 'Path length in km', 'rules' => 'trim')
		);

		$this->fields = array(
			'archive_id'
			,'event_id'
			,'station_id'
			,'image'
			,'image_folder'
			,'created_at'
			,'created_at_ms'
			,'mag'
			,'cam'
			,'lens'
			,'rstar'
			,'sec'
			,'av'
			,'vo'
			,'h1'
			,'h2'
			,'len'
		);
    }

    //get meteors for search
    function getArchives($filter = NULL, $start = 0, $limit = NULL)
    {
        $this->db->select('SQL_CALC_FOUND_ROWS `' . $this->db->dbprefix($this->table) . "`.*", FALSE);
        $order = 'created_at desc';
        
        if(!empty($filter)) {
            $conditions = $this->searchConditions($filter);
            $this->db->where($conditions);
            
            //order
            if(!empty($filter['order'])) {
                list($order_param, $order_direction) = explode(':', $filter['order']);
                $order = $order_param.' '.($order_direction == 'asc' ? 'asc' : 'desc');
            }
        }
        if ($limit != NULL)
        {
            $this->db->limit($limit, $start);
        }
        
        $this->db->order_by($order);
        
        $this->db->join('archive_events', 'event_id', 'LEFT');
        $this->db->select('event_name, event_slug, event_code');

        $this->db->join('archive_stations', 'station_id', 'LEFT');
        $this->db->select('station_name, station_slug, station_code');


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


	function find_stations_count($filter = NULL)
	{
		if ($filter != NULL)
		{
			$conditions = $this->searchConditions($filter);
            $this->db->where($conditions);
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

    //makes array of conditions for db query
    function searchConditions($filter) {
        $conditions = array();
        //station
        if(!empty($filter['station'])) {
            $conditions['station_slug'] = $filter['station'];
        }
        //event
        if(!empty($filter['event'])) {
            $conditions['event_slug'] = $filter['event_id'];
        }
        //date range
        if(!empty($filter['start'])) {
            $conditions['created_at >= '] = strtotime($filter['start']);
        }
        if(!empty($filter['end'])) {
            $conditions['created_at <= '] = strtotime($filter['end']." 12:00");
        }
        //name
        if(!empty($filter['name'])) {
            $conditions['image LIKE '] = "%".$filter['name']."%";
        }
        
        return $conditions;
    }

}

/* End of file Archives_model.php */
/* Location: ./application/models/Archives_model.php */
