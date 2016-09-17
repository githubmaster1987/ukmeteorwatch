<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*
 * HTML encode special characters Helper
 *
 * @access	public
 * @param	string
 * @return	string	HTML encoded string
 *
 */
function to_entities($string)
{
	$CI =& get_instance();
	return htmlentities($string, ENT_QUOTES, $CI->config->item('charset'));
}

function word_wrap_by_space($text, $len)   
{
    $split = explode(" ", $text);
    foreach($split as $key=>$value)   {
        if (strlen($value) > $len)    {
            $split[$key] = chunk_split($value, $len, "&#8203;");
        }
    }
	
    return implode(" ", $split);
}
/* End of file MY_string_helper.php */
/* Location: ./application/helpers/MY_string_helper.php */