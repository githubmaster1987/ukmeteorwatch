<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Determine if the request is an ajax request
 *
 * @access	public
 * @return	bool	is ajax?
 */
function async_request()
{
	$CI =& get_instance();
	return ($CI->input->server('HTTP_X_REQUESTED_WITH') && ($CI->input->server('HTTP_X_REQUESTED_WITH') == 'XMLHttpRequest'));
}

// --------------------------------------------------------------------

/**
 * Create JSON response from an array
 *
 * @access	public
 * @param	mixed	one dimensional array
 * @return	string	json object
 */
function json_response($in = array(), $return = FALSE)
{
	$out = '{';
	
	foreach ($in as $key => $value)
	{
		$out .= '"';
		$out .= $key;
		$out .= '":"';
		$out .= $value;
		$out .= '",';
	}
	
	// Remove stray comma at the end
	$out = substr($out, 0, -1);
	$out .= '}';
	
	if ($return)
	{
		return $out;
	}
	else
	{
		echo $out;
		exit;
	}
}

/* End of file ajax_helper.php */
/* Location: ./application/helpers/ajax_helper.php */