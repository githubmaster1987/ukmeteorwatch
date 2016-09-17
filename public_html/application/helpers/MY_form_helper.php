<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Form token based on Chris Shiflett's implementation
 * @see http://phpsecurity.org
*/

/**
 * Generate Form Token
 * 
 * @access	public
 * @return	string	unique form token
 * 
 */
function generate_token()
{
	$CI =& get_instance();
	
	$token = md5(uniqid(rand(), TRUE));
	$CI->session->set_userdata('token', $token);
	$CI->session->set_userdata('token_time', time());
	
	return $token;
}

// --------------------------------------------------------------------

/**
 * Check form token
 * 
 * @access	public
 * @param	string	submitted token
 * @param	integer	expiration time - default 5 minutes
 * @return	bool	token valid?
 * 
 */
function check_token($submitted_token, $expire = 300)
{
	$CI =& get_instance();
	
	$token_age = time() - $CI->session->userdata('token_time');
	$token = $CI->session->userdata('token');

	// 5 minutes to submit the form
	if ($token_age <= $expire)
	{
		if ($token === $submitted_token)
		{
			return TRUE;
		}		
	}
	
	// Expired or invalid
	$CI->session->unset_userdata('token');
	return FALSE;
}

// --------------------------------------------------------------------

/**
 * Create Captcha Image and add it to the db
 *
 * @access	public
 * @return	string	image tag with proper path
 */
function generate_captcha()
{
	$CI =& get_instance();
	
	$vals = array(
					'img_path'	 => './images/captcha/',
					'img_url'	 => '/images/captcha/',
					'font_path'	 => './images/fonts/4.ttf',
					'expiration' => 7200
				);

	$cap = create_captcha($vals);

	$data = array(
				'captcha_id'	=> '',
				'captcha_time'	=> $cap['time'],
				'ip_address'	=> $CI->input->ip_address(),
				'word'			=> $cap['word']
				);

	$query = $CI->db->insert('captcha', $data);
	
	return $cap['image'];
}

function day_month_year_dropdowns()
{
		$out = '';

        //"<select name='$did' id='$did'>";
		$days = array();
        for($i = 1; $i <= 31; $i++) {
			$days[$i] = $i;
        }
        $out .= form_dropdown($name = 'day', $days)."&nbsp;";

        $months = array(1 => "January", 2 => "February", 3 => "March", 4 => "April", 5 => "May", 6 => "June", 7 => "July", 8 => "August", 9 => "September", 10 => "October", 11 => "November", 12 => "December");
        $out .= form_dropdown($name = 'month', $months)."&nbsp;";

		for($i = date("Y"); $i <= date("Y") + 1; $i++) {
            $years[$i] = $i;
        }
        $out .= form_dropdown($name = 'year', $years)."&nbsp;";

        return $out;
}

function month_year_dropdowns($use_month_names = FALSE, $future_years=1, $default_month = array(), $default_year = array(), $divider = '&nbsp;', $months_extra='', $years_extra ='') {
		$out = '';

		if ($use_month_names === FALSE) {
			for($i = 1; $i <= 12; $i++) {
				$padMonth = str_pad($i, 2, '0', STR_PAD_LEFT);
				$months[$padMonth] = $padMonth;
			}
		} else {
			$months = array(1 => "January", 2 => "February", 3 => "March", 4 => "April", 5 => "May", 6 => "June", 7 => "July", 8 => "August", 9 => "September", 10 => "October", 11 => "November", 12 => "December");
		}

		$out .= form_dropdown($name = 'month', $months, $default_month, $months_extra);

		$out .= $divider;

		for($i = date("Y"); $i <= date("Y") + $future_years; $i++) {
            $years[$i] = $i;
        }
        $out .= form_dropdown($name = 'year', $years, $default_year, $years_extra);

        return $out;
}

	function form_checkbox_generator($input_name, $list, $default, $extra='')
	{
		$data = array();
		
		foreach($list as $item)
		{
			if(!empty($default))
			{
				$checked = in_array($item, $default) ? ' checked="checked"' : '';
			}
			else
			{
				$checked = '';
			}

			$data[] = '<input type="checkbox" name="'.$input_name.'[]" value="' . $item . '"'.$checked.' '.$extra.' />' . $item .  "\n";
		}
		
		return $data;
	}
	

	function form_multi_checkbox_generator($input_name, $list, $default, $extra='', $translate = FALSE)
	{
		$data = array();
		
		foreach($list as $k => $item)
		{
			if(!empty($default))
			{
				$checked = in_array($k, $default) ? ' checked="checked"' : '';
			}
			else
			{
				$checked = '';
			}

			$data[] = '<input type="checkbox" name="'.$input_name.'[]" value="' . $k . '"'.$checked.' '.$extra.' />' . ($translate ? _($item) : $item) .  "\n";
		}
		
		return $data;
	}	
	
// --------------------------------------------------------------------	

# Custom function to set checkboxes based on boolean setting in db

function selected_checkbox($boolean = 0)
{
	if ($boolean == TRUE)
	{
		return 'checked="checked"';
	}
}

// --------------------------------------------------------------------

function selected_radio($field_value = NULL, $value = NULL)
{
	if ($field_value == $value)
	{
		return 'checked="checked"';
	}
}

// --------------------------------------------------------------------	

function selected($field_value = NULL, $value = NULL)
{
	if ($field_value == $value)
	{
		return 'selected';
	}
}

// --------------------------------------------------------------------	

function db_checkbox($array = NULL, $db_field = NULL)
{
	if (!is_array($array))
	{
		return 0;
	}
	
	foreach($array as $key => $value)
	{
		if ($value == $db_field)
		{
			return 1;
		}
	}
	
	return 0;
}

// --------------------------------------------------------------------

function create_inventory_options()
{
	$inventories[''] = '0';
	
	for ($i = 1; $i <= 999; $i++) 
	{
		$inventory = str_pad($i, 2, '0', STR_PAD_LEFT);
		$inventories[$inventory] = $inventory;
	}
	
	return $inventories;
}


// ------------------------------------------------------------------------

/**
 * Textarea field
 *
 * @access	public
 * @param	mixed
 * @param	string
 * @param	string
 * @return	string
 */
if ( ! function_exists('form_textarea'))
{
	function form_textarea($data = '', $value = '', $extra = '')
	{
		$defaults = array('name' => (( ! is_array($data)) ? $data : ''), 'cols' => '90', 'rows' => '12');

		if ( ! is_array($data) OR ! isset($data['value']))
		{
			$val = $value;
		}
		else
		{
			$val = $data['value'];
			unset($data['value']); // textareas don't use the value attribute
		}

		$name = (is_array($data)) ? $data['name'] : $data;
		return "<textarea "._parse_form_attributes($data, $defaults).$extra.">".$val."</textarea>";
	}
}

/* End of file MY_form_helper.php */
/* Location: ./application/helpers/MY_form_helper.php */