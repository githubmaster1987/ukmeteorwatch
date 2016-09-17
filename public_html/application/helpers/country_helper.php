<?php

function get_country_id_by_ip($ip = '', $method = 'db_geoip')
{
	$CI = & get_instance();
	
	$CI->load->model('countries_model');
	
	$country_id = 0; // Undefined country
	
	$info = FALSE;
	
	if ($method == 'pecl_geoip')
	{
		$info = @geoip_record_by_name($ip);
		$info = array('iso3' => $info['country_code3']);
	} 
	elseif($method == 'db_geoip')
	{
		// http://www.maxmind.com/app/csv
		// 
		// TODO: convert ip to number directly
		// TODO: add country_id to table and Import country_id from countries table
		
		$query = $CI->db->query("SELECT geoip_iso2 FROM geoip3 WHERE geoip_start <= inet_aton(?) AND geoip_end >= inet_aton(?) LIMIT 1", array($ip, $ip));

		if ($query->num_rows() > 0)
		{
			$row = $query->row(); 
			$info = array('code' => $row->geoip_iso2);
		}
	}
	
	if ($info !== FALSE)
	{
		$id = $CI->countries_model->field($info, 'id');
		
		if ($id !== FALSE)
		{
			$country_id = $id;
		}
	}
	
	return $country_id;
}

