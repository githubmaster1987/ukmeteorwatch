<?php

if (!defined('BASEPATH')) exit('No direct script access allowed');

class MY_Form_validation extends CI_Form_validation
{

	function __construct($rules = array())
	{
		parent::__construct($rules);
		$this->CI->load->language('extra_validation');
		$this->CI->load->helper('password');
	}

	/**
	 * Alpha-numeric with underscores dots and dashes
	 *
	 * @access	public
	 * @param	string
	 * @return	bool
	 */
	function alpha_dot_dash($str)
	{
		return (!preg_match("/^([-a-z0-9_\-\.])+$/i", $str)) ? FALSE : TRUE;
	}

	function strong_password($str)
	{

		if (!preg_match("#[0-9]+#", $str))
		{
			$this->set_message('strong_password', "Password must include at least one number!");
			return FALSE;
		}

		if (!preg_match("#[a-z]+#", $str))
		{
			$this->set_message('strong_password', "Password must include at least one letter!");
			return FALSE;
		}


		$points = check_strength($str, 1);

		//quark_dump($points);

		if ($points < 50)
		{
			$this->set_message('strong_password', "Password is not strong enough.");
			return FALSE;
		}

		return TRUE;
	}

	function is_path_writeable($str = NULL, $path = NULL)
	{
		if (!empty($path))
		{
			// Make sure it has a trailing slash
			$path = rtrim($path, '/') . '/';

			clearstatcache();

			if (!@is_dir($path))
			{
				@mkdir($path);
				@chmod($path, 0755);
			}

			if (!@is_dir($path))
			{
				$this->set_message('is_path_writeable', "Cant't create {$path} directory. Please check permissions on the parent directory.");
				return FALSE;
			}


			/* 			if( ($perm = substr(decoct(fileperms($path)),2)) != '777' )
			  {
			  $this->set_message('is_path_writeable', "{$path} directory is not writeable. Currently you have {$perm} permissions on the directory. Please chmod 777 on the directory");
			  return FALSE;
			  }
			 */
			if (!is_writable($path))
			{
				$this->set_message('is_path_writeable', "{$path} directory is not writeable. Currently you have {$perm} permissions on the directory. Please chmod 777 on the directory");
				return FALSE;
			}
		}

		return TRUE;
	}

	/**
	 * Formats an UTF-8 string and removes potential harmful characters
	 *
	 * @access	public
	 * @param	string
	 * @return	string
	 * @author	Jeroen v.d. Gulik
	 * @since	v1.0-beta1
	 * @todo	Find decent regex to check utf-8 strings for harmful characters
	 */
	function utf8($str)
	{
		// If they don't have mbstring enabled (suckers) then we'll have to do with what we got
		if (!function_exists($str))
		{
			return $str;
		}

		$str = mb_convert_encoding($str, 'UTF-8', 'UTF-8');

		return htmlentities($str, ENT_QUOTES, 'UTF-8');
	}

	/**
	 * Check for normal punctuation only
	 *
	 * This function checks to ensure that only normal punctuation used in names
	 * and simple texts exists in a string
	 *
	 * Allowed: . , ? ! @ # $ % & * ( ) " '
	 *
	 * @param string $str
	 * @return boolean
	 */
	function normal_punc_only($str)
	{
		$allowed = array('a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z', 'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z', '.', ',', '?', '!', '@', '#', '$', '%', '&', '*', '(', ')', '"', '\'');

		for ($x = 0; $x < strlen($str); $x++)
		{
			if (!in_array(substr($str, $x, 1), $allowed))
			{
				$this->set_message("normal_punc_only", "The %s must not contain any special punctuation or spaces.  Normal punctuation only.");
				return FALSE;
			}
		}

		//IF made it here
		return TRUE;
	}

	// --------------------------------------------------------------------

	/**
	 * Checks for normal punctuation only, also allow spaces
	 *
	 * This function behaves identically to normal_punc_only(), but also
	 * allows spaces
	 * @see normal_punc_only()
	 *
	 * @param string $str
	 * @return boolean
	 */
	function normal_punc_space($str)
	{
		$allowed = array(' ', 'a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z', 'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z', '.', ',', '?', '!', '@', '#', '$', '%', '&', '*', '(', ')', '"', '\'', ':', ';');

		for ($x = 0; $x < strlen($str); $x++)
		{
			if (!in_array(substr($str, $x, 1), $allowed))
			{
				$this->set_message("normal_punc_space", "The %s must not contain any special punctuation or spaces.  Normal punctuation only.");
				return FALSE;
			}
		}

		//IF made it here
		return TRUE;
	}

	// --------------------------------------------------------------------

	/**
	 * Check if string meets password rules
	 *
	 * This function allows you to define password rules and check to see
	 * if a string meets those rules
	 *
	 * @param string $str
	 * @return boolean
	 */
	function meets_password_rules($str)
	{
		if (strlen($str) < 6)
		{
			$this->set_message('meets_password_rules', "The %s must be at least six characters long!");
			return FALSE;
		}

		//If Made it here
		return TRUE;
	}

	// --------------------------------------------------------------------

	/**
	 * Checks to see if a date is formatted properly
	 *
	 * This function depends on the is_valid_date() function in the date helper
	 * @see is_valid_date()
	 *
	 * @param string $str
	 * @return boolean
	 */
	function valid_date($str)
	{
		$ci = & get_instance();
		$ci->load->helper('date');
		if (is_valid_date($str)) return TRUE;
		else
		{
			$this->set_message('valid_date', "The %s field must be a valid date");
			return FALSE;
		}
	}

	// --------------------------------------------------------------------

	/**
	 * Checks to see if a string contains any HTML tags
	 *
	 * @param string $str
	 * @return boolean
	 */
	function notags($str)
	{
		if (strpos($str, "<") OR strpos($str, ">"))
		{
			$this->set_message('notags', "The %s field cannot contain any code!");
			return FALSE;
		}
		else return TRUE;
	}

	// --------------------------------------------------------------------

	/**
	 * Checks to see if a string contains a valid URL
	 *
	 * @author http://www.geekzilla.co.uk/View2D3B0109-C1B2-4B4E-BFFD-E8088CBC85FD.htm
	 * @param string $str
	 * @return boolean
	 */
	function valid_url($str)
	{
		$str = trim($str);

		$regex = "((https?|ftp|gopher|telnet|file|notes|ms-help):((//)|(\\\\))+[\w\d:#@%/;$()~_?\+-=\\\.&]*)";

		//Use preg_match_all instead of preg_match to ensure
		//that the string contains only ONE URL; not several.
		$temp = array();
		if (preg_match_all($regex, $str, $temp) === 1) return TRUE;
		else
		{
			$this->set_message('valid_url', "The %s field must be a valid URL!");
			return FALSE;
		}
	}

	// --------------------------------------------------------------------

	/**
	 * Checks to see if a string contains a specific value
	 *
	 * If the string contains the value, return true, otherwise
	 * return false
	 *
	 * @param string $str
	 * @param string $val
	 * @return boolean
	 */
	function contains($str, $val)
	{
		if (strpos($str, $val) !== FALSE) return TRUE;
		else
		{
			$this->set_message('contains', "The %s field must contain '$val'");
			return FALSE;
		}
	}

	// --------------------------------------------------------------------


	function check_uk_postcodes($codes = '')
	{
		$codes = preg_split('~,~', $codes, -1, PREG_SPLIT_NO_EMPTY);

		foreach ($codes as $code)
		{
			$code = trim($code);

			if (!preg_match('~(?P<part1>GIR 0AA|[A-PR-UWYZ]([0-9][0-9A-HJKPS-UW]?|[A-HK-Y][0-9][0-9ABEHMNPRV-Y]?))\s{0,}(?P<part2>[0-9][ABD-HJLNP-UW-Z]{2}){0,1}~is', $code, $matches))
			{
				$this->set_message('check_uk_postcodes', $code . ' is invalid Postcode');
				return FALSE;
			}
		}

		return TRUE;
	}

	function valid_uk_postcode($code)
	{
		if (!preg_match('~(GIR 0AA)|(((A[BL]|B[ABDHLNRSTX]?|C[ABFHMORTVW]|D[ADEGHLNTY]|E[HNX]?|F[KY]|G[LUY]?|H[ADGPRSUX]|I[GMPV]|JE|K[ATWY]|L[ADELNSU]?|M[EKL]?|N[EGNPRW]?|O[LX]|P[AEHLOR]|R[GHM]|S[AEGKLMNOPRSTY]?|T[ADFNQRSW]|UB|W[ADFNRSV]|YO|ZE)[1-9]?[0-9]|((E|N|NW|SE|SW|W)1|EC[1-4]|WC[12])[A-HJKMNPR-Y]|(SW|W)([2-9]|[1-9][0-9])|EC[1-9][0-9]) [0-9][ABD-HJLNP-UW-Z]{2})~is', $code, $matches))
		{
			$this->set_message('valid_uk_postcode', 'Invalid Postcode');
			return FALSE;
		}
		return TRUE;
	}


	function is_valid_month_year()
	{
		$this->CI->load->helper('date_helper');
		$this->_error_messages['is_valid_month_year'] = '';

		$year = $this->CI->input->post('year');
		$month = $this->CI->input->post('month');

		if (mktime(23, 59, 59, $_POST['month'], days_in_month($_POST['month']), $_POST['year']) < mktime())
		{
			$this->_error_messages['is_valid_month_year'] = 'Please select future Expiration date';
			return FALSE;
		}
	}

}