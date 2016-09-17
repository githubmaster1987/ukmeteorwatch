<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Confirmation before executing a controller function.
 *
 * By adding a 'yes' segment to the url, we know that
 * the user confirmed our action.
 *
 * @access	public
 */
function confirm($message = 'Are you sure?')
{
	$CI	=& get_instance();
    $CI->output->set_header("Cache-Control: no-store, no-cache, must-revalidate");
    $CI->output->set_header("Cache-Control: post-check=0, pre-check=0", FALSE);
    $CI->output->set_header("Pragma: no-cache");
	$segments = $CI->uri->segment_array();
	
	if( end($segments) != 'yes')
	{
		$referrer = $CI->input->server('HTTP_REFERER');
		if (empty($referrer))
		{
			// No user agent referrer, use the controller index
			$RTR =& load_class('Router');
			$referrer = $RTR->fetch_directory().$RTR->fetch_class();
		}

		$data['no_href']	= $referrer;
		$data['yes_href']	= implode('/', $segments).'/yes';
		$data['message']	= $message;
				
		echo $CI->template->display('content/confirmation', $data, 'general', TRUE);
		exit;
	}
}

/* End of file confirm_helper.php */
/* Location: ./application/helpers/confirm_helper.php */