<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Archive_events_model extends MY_Model
{
	function __construct()
	{
		parent::__construct();

		$this->table = 'archive_events';
		$this->primaryKey = 'event_id';
		$this->returnArray = FALSE;

		$this->validation_rules = array(
			array('field' => 'event_id', 'label' => 'Event Id', 'rules' => 'trim')
			,array('field' => 'event_code', 'label' => 'Event Folder', 'rules' => 'trim|required')
			,array('field' => 'event_slug', 'label' => 'Event Slug', 'rules' => 'trim|required')
			,array('field' => 'event_name', 'label' => 'Event Name', 'rules' => 'trim|required')
		);

		$this->fields = array(
			'event_id'
			,'event_code'
			,'event_slug'
			,'event_name'
		);
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
		    $fields_ar = explode(',', $fields);
            $fields_sel = '';
            foreach($fields_ar as $field) {
                if(!empty($fields_sel)) $fields_sel .= ', ';
                $fields_sel .= $this->table .'.'. trim($field);
            }
			if ($calc_found_rows)
			{
				$this->db->select('SQL_CALC_FOUND_ROWS ' . $fields_sel, FALSE);
			}
			elseif(stripos($fields, 'DISTINCT') !== FALSE)
			{
				$this->db->select($fields, FALSE);
			}
			else
			{
				$this->db->select($fields_sel);
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

//		$this->db->select('(SELECT COUNT(*) FROM archives WHERE archives.event_id = archive_events.event_id) AS archives_cnt');
        $this->db->join('archives', 'archives.event_id = archive_events.event_id', 'inner');
        $this->db->group_by("archive_events.event_id");

		$query = $this->db->get($this->table);
		$this->numRows = $query->num_rows();

		return ($this->returnArray) ? $query->result_array() : $query->result();
	}

    public function generateFullList($conditions = NULL, $order = NULL, $start = 0, $limit = NULL, $key = NULL, $value = NULL, $default = array()) {
        if (!is_array($default))
        {
            $default = array('' => $default);
        }

        $return = $default;
        
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
        
        $fields_ar = array($key, $value);
        $fields_sel = '';
        foreach($fields_ar as $field) {
            if(!empty($fields_sel)) $fields_sel .= ', ';
            $fields_sel .= $this->table .'.'. trim($field);
        }
        $this->db->select($fields_sel);
        if ($order != NULL)
        {
            $this->db->order_by($order);
        }

        if ($limit != NULL)
        {
            $this->db->limit($limit, $start);
        }
        
        $query = $this->db->get($this->table);
        $this->numRows = $query->num_rows();

        if ($data = $query->result())
        {
            foreach ($data as $row)
            {
                $return[$row->$key] = $row->$value;
            }
        }

        return $return;
        
    }

    /*
     * Gets events array for archive automplete
     * @param $filter array 
     */
    public function autocompleteSelect($filter = '') {
        $events = $this->findAll(NULL, '*', 'event_name ASC');
        $result = array();
        foreach($events as $key=>$event){
            $result[$key]['id'] = $event->event_slug;
            $result[$key]['name'] = $event->event_name.' - '.strtoupper($event->event_code);
            $result[$key]['url'] = '';
        }
       
        return $result;
    }
}

/* End of file Archive_events_model.php */
/* Location: ./application/models/Archive_events_model.php */
