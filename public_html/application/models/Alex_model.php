<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Alex_model extends MY_Model
{
	function __construct()
	{
		parent::__construct();
	}

    function getFromArchiveTable ($station_id)
    {

        //SELECT * FROM archives WHERE date(created_at) BETWEEN "2013-07-01" AND "2013-08-01" AND station_id =4
//        $query = $this->db->query('SELECT count(*),FROM_UNIXTIME(created_at,"%Y-%m-%d") as date_new FROM archives WHERE station_id = 4 HAVING date_new BETWEEN "2013-07-01" AND "2013-08-01" ');
        $query = $this->db->query('SELECT count(*),FROM_UNIXTIME(created_at,"%Y-%m") as date_new FROM archives WHERE station_id = 4    GROUP  BY date_new');

        $row = $query->result();
        return $row;
    }

}

/* End of file Archive_events_model.php */
/* Location: ./application/models/Archive_events_model.php */
