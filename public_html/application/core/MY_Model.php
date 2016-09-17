<?php

if (!defined('BASEPATH')) exit('No direct script access allowed');

class MY_Model extends CI_Model
{

	public $table;
	public $primaryKey = 'id';
	public $fields = array();
	public $jqgrid_fields = array();
	public $validation_rules = array();
	public $insertId = NULL;
	public $numRows = NULL;
	public $affectedRows = NULL;
	public $returnArray = TRUE;
	public $jqgrid_qopers = array(
		'eq' => " = ",
		'ne' => " <> ",
		'lt' => " < ",
		'le' => " <= ",
		'gt' => " > ",
		'ge' => " >= ",
		'bw' => " LIKE ",
		'bn' => " NOT LIKE ",
		'in' => " IN ",
		'ni' => " NOT IN ",
		'ew' => " LIKE ",
		'en' => " NOT LIKE ",
		'cn' => " LIKE ",
		'nc' => " NOT LIKE "
	);

	public function __construct()
	{
		parent::__construct();

		log_message('debug', "Extended Model Class Initialized");
	}

	function loadTable($table, $primaryKey = 'id', $fields = array())
	{
		$this->table = $table;
		$this->primaryKey = $primaryKey;

		$this->fields = (!empty($fields)) ? $fields : $this->db->list_fields($table);
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

		$query = $this->db->get($this->table);
		$this->numRows = $query->num_rows();

		return ($this->returnArray) ? $query->result_array() : $query->result();
	}

	function find($conditions = NULL, $fields = '*', $order = NULL)
	{
		$data = $this->findAll($conditions, $fields, $order, 0, 1);

		if ($data)
		{
			return $data[0];
		}

		return FALSE;
	}

	function field($conditions = NULL, $name, $fields = '*', $order = NULL)
	{
		$data = $this->findAll($conditions, $fields, $order, 0, 1);

		if ($data)
		{
			$row = $data[0];

			if($this->returnArray && isset($row[$name]))
			{
				return $row[$name];
			}
			elseif(!$this->returnArray && isset($row->{$name}))
			{
				return $row->{$name};
			}
		}

		return FALSE;
	}

	function findCount($conditions = NULL)
	{
		$data = $this->findAll($conditions, 'COUNT(*) AS count', NULL, 0, 1);

		if ($data)
		{

			return $this->returnArray ? (int)$data[0]['count'] : (int)$data[0]->count ;
		}

		return FALSE;
	}

	/**
	 * Returns a key value pair array from database matching given conditions.
	 *
	 * Example use: generateList(NULL, '', 0. 10, 'id', 'username');
	 * Returns: array('10' => 'emran', '11' => 'hasan')
	 *
	 *
	 * @return array a list of key val ue pairs given criteria
	 * @access public
	 */
	function generateList($conditions = NULL, $order = NULL, $start = 0, $limit = NULL, $key = NULL, $value = NULL, $default = array())
	{

		if (!is_array($default))
		{
			$default = array('' => $default);
		}

		$return = $default;

		if ($data = $this->findAll($conditions, "{$key}, {$value}", $order, $start, $limit))
		{
			foreach ($data as $row)
			{
				if ($this->returnArray)
				{
					$return[$row[$key]] = $row[$value];
				}
				else
				{
					$return[$row->$key] = $row->$value;
				}
			}
		}

		return $return;

	}

	/**
	 * Returns an array of the values of a specific column from database matching given conditions.
	 *
	 * Example use: generateSingleArray(NULL, 'name');
	 *
	 *
	 * @return array a list of key value pairs given criteria
	 * @access public
	 */
	function generateSingleArray($conditions = NULL, $field = NULL, $order = NULL, $start = 0, $limit = NULL)
	{
		$data = $this->findAll($conditions, "$field", $order, $start, $limit);

		if ($data)
		{
			foreach ($data as $row)
			{
				$arr[] = ($this->returnArray) ? $row[$field] : $row->$field;
			}

			return $arr;
		}
		else
		{
			return FALSE;
		}
	}

	/**
	 * Returns a list of fields from the database and saves in the model
	 *
	 *
	 * @return array Array of database fields
	 * @access public
	 */
	function read($id = NULL, $fields = NULL)
	{
		if (!empty($id))
		{
			return $this->find(array($this->table . '.' . $this->primaryKey => $id), $fields);
		}

		return FALSE;
	}

	/**
	 * Inserts a new record in the database.
	 *
	 *
	 * @return boolean success
	 * @access public
	 */
	function insert($data = NULL)
	{
		if (empty($data))
		{
			return FALSE;
		}

		$data['created_at'] = $data['updated_at'] = date("Y-m-d H:i:s");

		foreach ($data as $key => $value)
		{
			if (array_search($key, $this->fields) === FALSE)
			{
				unset($data[$key]);
			}
		}

		$this->db->insert($this->table, $data);
		$this->insertId = $this->db->insert_id();

		return $this->insertId;
	}

	public function save($data = NULL, $id = NULL)
	{
		return $this->update($data, $id);
	}

	public function update($data = NULL, $id = NULL)
	{
		if ($data == NULL)
		{
			return FALSE;
		}

		$data['updated_at'] = $now = date('Y-m-d H:i:s');

		if(empty($id) && empty($data['created_at']))
		{
			$data['created_at'] = $now;
		}

		foreach ($data as $key => $value)
		{
			if (array_search($key, $this->fields) === FALSE)
			{
				unset($data[$key]);
			}
		}

		if ($id !== NULL)
		{
			$this->db->where($this->primaryKey, $id);
			$this->db->update($this->table, $data);
			$this->affectedRows = $this->db->affected_rows();
			return $id;
		}
		else
		{
			$this->db->insert($this->table, $data);
			$this->insertId = $this->db->insert_id();
			return $this->insertId;
		}
	}

	public function __call($method, $args)
	{
		$watch = array('findBy', 'findAllBy');

		foreach ($watch as $found)
		{
			if (stristr($method, $found))
			{
				$field = strtolower(str_replace($found, '', $method));
				return $this->$found($field, $args);
			}
		}
	}

	public function findBy($field, $value)
	{
		$where = array($field => $value);
		return $this->find($where);
	}

	public function findAllBy($field, $value)
	{
		$where = array($field => $value);
		return $this->findAll($where);
	}

	public function executeQuery($sql)
	{
		return $this->db->query($sql);
	}

	public function getLastQuery()
	{
		return $this->db->last_query();
	}

	public function getInsertString($data)
	{
		return $this->db->insert_string($this->table, $data);
	}

	public function getFields()
	{
		return $this->fields;
	}

	function remove($id = NULL)
	{
		if (empty($id))
		{
			return FALSE;
		}

		return $this->db->delete($this->table, array($this->primaryKey => $id));
	}

	function removeBatch($values = array(), $field = 'id')
	{
		$this->db->where_in($field, $values);

		if ($this->db->delete($this->table))
		{
			return TRUE;
		}
		else
		{
			return FALSE;
		}
	}

	/**
	 * Returns a resultset for given SQL statement. Generic SQL queries should be made with this method.
	 *
	 *
	 * @return array Resultset
	 * @access public
	 */
	function query($sql)
	{
		return $this->db->query($sql);
	}

	/**
	 * Returns the last query that was run (the query string, not the result).
	 *
	 *
	 * @return string SQL statement
	 * @access public
	 */
	function lastQuery()
	{
		return $this->db->last_query();
	}

	/**
	 * This function simplifies the process of writing database inserts. It returns a correctly formatted SQL insert string.
	 *
	 *
	 * @return string SQL statement
	 * @access public
	 */
	function insertString($data)
	{
		return $this->db->insert_string($this->table, $data);
	}

	public function getInsertId()
	{
		return $this->insertId;
	}

	public function getNumRows()
	{
		return $this->numRows;
	}

	public function getAffectedRows()
	{
		return $this->affectedRows;
	}

	public function setPrimaryKey($primaryKey)
	{
		$this->primaryKey = $primaryKey;
	}

	public function setReturnArray($returnArray)
	{
		$this->returnArray = $returnArray;
	}

	function getEnumFieldValues($field = NULL)
	{

		$sql = "SHOW COLUMNS FROM {$this->table} WHERE Field = ?";
		$query = $this->db->query($sql, array($field));

		if ($query->num_rows() > 0)
		{
			$type = $query->row()->Type;
			$type = preg_replace("/(enum|set)\('(.+?)'\)/i", "\\2", $type);

			$fieldSplit = preg_split("/','/", $type);

			$types = array();

			foreach ($fieldSplit as $type)
			{
				$types[(string) $type] = form_prep(ucfirst($type));
			}

			return $types;
		}

		return FALSE;
	}

	/**
	 * Checks if $value for $field is already used
	 *
	 * @access	private
	 * @param	string	email
	 * @return	bool
	 */
	function checkUnique($field, $value)
	{
		$this->db->select($field);
		$this->db->where($field, $value);
		$this->db->limit(1);

		return ($this->db->count_all_results($this->table) > 0) ? TRUE : FALSE;
	}

	function isUnique($field, $value)
	{
		$this->db->select($field);
		$this->db->where($field, $value);
		$this->db->limit(1);

		return ($this->db->count_all_results($this->table) > 0) ? FALSE : TRUE;
	}

	function jqgridToSql($field, $oper, $val)
	{
		// we need here more advanced checking using the type of the field - i.e. integer, string, float

		switch ($field)
		{
			case 'id':
				return intval($val);
				break;
			case 'amount':
			case 'tax':
			case 'total':
				return floatval($val);
				break;
			default :
				if ($oper == 'bw' || $oper == 'bn') return "'" . $this->db->escape_like_str($val) . "%'";
				else if ($oper == 'ew' || $oper == 'en') return "'%" . $this->db->escape_like_str($val) . "'";
				else if ($oper == 'cn' || $oper == 'nc') return "'%" . $this->db->escape_like_str($val) . "%'";
				else return $this->db->escape($val);
		}
	}

	function fromArray($data = array(), $fields = array())
	{
		$record = $this->returnArray ? array() : new stdClass();

		if (empty($fields))
		{
			$fields = $this->fields;
		}
		// If $fields is provided, assume all $fields should exist.
		foreach ($fields as $f)
		{
			// Otherwise, if the $data was set, store it...
			if (isset($data[$f]))
			{
				$v = $data[$f];
			}
			else
			{
				// Or assume it was an unchecked checkbox, and clear it.
				$v = NULL;
			}

			if($this->returnArray )
			{
				$record[$f] = $v;
			}
			else {
				$record->{$f} = $v;
			}
		}

		return $record;
	}

	function emptyRecord()
	{
		if ($this->returnArray)
		{
			$data = array();

			foreach ($this->fields as $field)
			{
				$data[$field] = NULL;
			}
		}
		else
		{
			$data = new stdClass();

			foreach ($this->fields as $field)
			{
				$data->{$field} = NULL;
			}
		}

		return $data;
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

		$query = $this->db->get($this->table);

		$this->numRows = $query->num_rows();

		return ($this->returnArray) ? $query->result_array() : $query->result();
	}

}