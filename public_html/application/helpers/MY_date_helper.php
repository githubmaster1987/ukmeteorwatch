<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * CodeIgniter Date Helpers
 *
 * @package		CodeIgniter
 * @subpackage	Helpers
 * @category	Helpers
 */

// ------------------------------------------------------------------------

function format_date($unix, $format = '')
{
	if ($unix == '' || ! is_numeric($unix))
	{
		$unix = strtotime($unix);
	}

	if ( ! $format)
	{
		$format = Settings::get('date_format');
	}

	return strstr($format, '%') !== FALSE
		? ucfirst(utf8_encode(strftime($format, $unix))) //or? strftime($format, $unix)
		: date($format, $unix);
}

// ------------------------------------------------------------------------
/**
* Convert MySQL's DATE (YYYY-MM-DD) or DATETIME (YYYY-MM-DD hh:mm:ss) to timestamp
*
* Returns the timestamp equivalent of a given DATE/DATETIME
*
* @todo add regex to validate given datetime

* @access    public
* @return    integer
*/

	function mysqldatetime_to_timestamp($datetime = "")
	{
	  // function is only applicable for valid MySQL DATETIME (19 characters) and DATE (10 characters)
	  $l = strlen($datetime);
	    if(!($l == 10 || $l == 19 || $l == 16))
	    {
	      return false;
	    }

	    //
	    $date = $datetime;
	    $hours = 0;
	    $minutes = 0;
	    $seconds = 0;

	    // DATETIME only
	    if($l == 19)
	    {
	      list($date, $time) = explode(" ", $datetime);
	      list($hours, $minutes, $seconds) = explode(":", $time);
	    }
	    if($l == 16)
	    {
	      list($date, $time) = explode(" ", $datetime);
	      list($hours, $minutes) = explode(":", $time);
	    }

	    list($year, $month, $day) = explode("-", $date);

	    return mktime($hours, $minutes, $seconds, $month, $day, $year);
	}

// ------------------------------------------------------------------------

/**
* Convert MySQL's DATE (YYYY-MM-DD) or DATETIME (YYYY-MM-DD hh:mm:ss) to date using given format string
*
* Returns the date (format according to given string) of a given DATE/DATETIME
*

* @access    public
* @return    integer
*/
function mysqldatetime_to_date($datetime = "", $format = "d.m.Y, H:i:s")
{
	if(empty($datetime) || $datetime == '0000-00-00 00:00:00' || $datetime == '0000-00-00')
	{
		return FALSE;
	}

    return date($format, mysqldatetime_to_timestamp($datetime));
}

// ------------------------------------------------------------------------

/**
* Convert timestamp to MySQL's DATE or DATETIME (YYYY-MM-DD hh:mm:ss)
*
* Returns the DATE or DATETIME equivalent of a given timestamp
*

* @access    public
* @return    string
*/
function timestamp_to_mysqldatetime($timestamp = "", $datetime = true)
{
  if(empty($timestamp) || !is_numeric($timestamp)) $timestamp = time();

    return ($datetime) ? date("Y-m-d H:i:s", $timestamp) : date("Y-m-d", $timestamp);
}

// ------------------------------------------------------------------------

/**
* Convert timestamp to Human Date
*
* Returns the date (format according to given string) of a given timestamp
*

* @access    public
* @param     string
* @param     string
* @return    string
*/
function timestamp_to_date($timestamp = "", $format = "d/m/Y H:i:s")
{
  if(empty($timestamp) || !is_numeric($timestamp)) $timestamp = time();
  return date($format, $timestamp);
}

// ------------------------------------------------------------------------

/**
* Convert Human Date to Timestamp
*
* Returns the timestamp equivalent of a given HUMAN DATE/DATETIME
*

* @access    public
* @param     string
* @return    integer
*/
function date_to_timestamp($datetime = "")
{
  if (!preg_match("/^(\d{1,2})[.\- \/](\d{1,2})[.\- \/](\d{2}(\d{2})?)( (\d{1,2}):(\d{1,2})(:(\d{1,2}))?)?$/", $datetime, $date))
    return FALSE;

  $day = $date[1];
  $month = $date[2];
  $year = $date[3];
  $hour = (empty($date[6])) ? 0 : $date[6];
  $min = (empty($date[7])) ? 0 : $date[7];
  $sec = (empty($date[9])) ? 0 : $date[9];

  return mktime($hour, $min, $sec, $month, $day, $year);
}

// ------------------------------------------------------------------------

/**
* Convert HUMAN DATE to MySQL's DATE or DATETIME (YYYY-MM-DD hh:mm:ss)
*
* Returns the DATE or DATETIME equivalent of a given HUMAN DATE/DATETIME
*

* @access    public
* @param     string
* @param     boolean
* @return    string
*/
function date_to_mysqldatetime($date = "", $datetime = TRUE)
{
  return timestamp_to_mysqldatetime(date_to_timestamp($date), $datetime);
}


// ------------------------------------------------------------------------
    function valid_mysql_date($date)
    {
      //match the format of the date
      if (preg_match ("/^([0-9]{4})-([0-9]{2})-([0-9]{2})$/", $date, $parts))
      {
        //check weather the date is valid of not
            if(checkdate($parts[2],$parts[3],$parts[1]))
              return true;
            else
             return false;
      }
      else
        return false;
    }

    function valid_us_date($date)
    {
      //match the format of the date
      if (preg_match ("/^([0-9]{2})\/([0-9]{2})\/([0-9]{4})$/", $date, $parts))
      {
        //check weather the date is valid of not
            if(checkdate($parts[1],$parts[2],$parts[3]))
              return true;
            else
             return false;
      }
      else
        return false;
    }
// ------------------------------------------------------------------------

	function valid_mysql_datetime($date)
    {
      //match the format of the date
      if (preg_match ("/^([0-9]{4})-([0-9]{2})-([0-9]{2}) ([01][0-9]|2[0-3]):([0-5][0-9])$/", $date, $parts))
      {
        //check weather the date is valid of not
            if(checkdate($parts[2],$parts[3],$parts[1]))
              return true;
            else
             return false;
      }
      else
        return false;
    }
// ------------------------------------------------------------------------


//$datetime_start - expects a strtotime compatible value
//$datetime_end - expects a strtotime compatible value
//$interval - can take either a strtotime compatible modifier (i.e.: -1 day), or an integer,
//	which will be perceived as total seconds and converted to a strtotime compatible modifier
//$format - the format of the key for the array's key/value pair (see php.net/date for formatting options)
//$value - the default value to be set for the blank array which will be used in a followup array_merge call.
//TIME FOR AN EXAMPLE OF USAGE:
//$myArray = array('10/01' =>3, '10/04' => 7, '10/05' => 4);
//$default = datetime_range('2009-10-01 00:00:00', '2009-10-05 00:00:00', '1 day', 'm/d');
//$myArray = array_merge($default, $myArray);
//print_r($myArray);

/************ this would have printed ************
Array(
	[10/01] => 3
	[10/02] => 0
	[10/03] => 0
	[10/04] => 7
	[10/05] => 4
)
********************************************/
function datetime_range($datetime_start, $datetime_stop, $interval, $format, $value = 0){
	if(is_int($interval)){
		$interval = $interval . ' seconds';
	}
	$range = array();
	$datetime_start = strtotime($datetime_start);
	$datetime_stop  = strtotime($datetime_stop);
	for($curTime = $datetime_start; $curTime <= $datetime_stop; $curTime = strtotime(date('r', $curTime) . ' + '.$interval)){
		$range[date($format, $curTime)] = $value;
	}
	return $range;
}

/* Works out the time since the entry post, takes a an argument in unix time (seconds) */
function time_since($original) {
    // array of time period chunks
    $chunks = array(
        array(60 * 60 * 24 * 365 , 'year'),
        array(60 * 60 * 24 * 30 , 'month'),
        array(60 * 60 * 24 * 7, 'week'),
        array(60 * 60 * 24 , 'day'),
        array(60 * 60 , 'hour'),
        array(60 , 'min'),
    );

    $today = time(); /* Current unix time  */
    $since = $today - $original;

    // $j saves performing the count function each time around the loop
    for ($i = 0, $j = count($chunks); $i < $j; $i++) {

        $seconds = $chunks[$i][0];
        $name = $chunks[$i][1];

        // finding the biggest chunk (if the chunk fits, break)
        if (($count = floor($since / $seconds)) != 0) {
            // DEBUG print "<!-- It's $name -->\n";
            break;
        }
    }

    $print = ($count == 1) ? '1 '.$name : "$count {$name}s";

    if ($i + 1 < $j) {
        // now getting the second item
        $seconds2 = $chunks[$i + 1][0];
        $name2 = $chunks[$i + 1][1];

        // add second item if it's greater than 0
        if (($count2 = floor(($since - ($seconds * $count)) / $seconds2)) != 0) {
            $print .= ($count2 == 1) ? ', 1 '.$name2 : ", $count2 {$name2}s";
        }
    }
    return $print;
}


	function timespan_custom($seconds = 1, $time = '')
	{
		$CI =& get_instance();
		$CI->lang->load('date');

		if ( ! is_numeric($seconds))
		{
			$seconds = 1;
		}

		if ( ! is_numeric($time))
		{
			$time = time();
		}

		$seconds = ($time <= $seconds) ? 1 : $time - $seconds;

		$str = '';
		$years = floor($seconds / 31536000);

		if ($years > 0)
		{
			$str .= $years.' '.$CI->lang->line((($years	> 1) ? 'date_years' : 'date_year')).', ';
		}

		$seconds -= $years * 31536000;
		$months = floor($seconds / 2628000);

		if ($years > 0 OR $months > 0)
		{
			if ($months > 0)
			{
				$str .= $months.' '.$CI->lang->line((($months	> 1) ? 'date_months' : 'date_month')).', ';
			}

			$seconds -= $months * 2628000;
		}

		$weeks = floor($seconds / 604800);

		if ($years > 0 OR $months > 0 OR $weeks > 0)
		{
			if ($weeks > 0)
			{
				$str .= $weeks.' '.$CI->lang->line((($weeks	> 1) ? 'date_weeks' : 'date_week')).', ';
			}

			$seconds -= $weeks * 604800;
		}

		$days = floor($seconds / 86400);

		if ($months > 0 OR $weeks > 0 OR $days > 0)
		{
			if ($days > 0)
			{
				$str .= $days.' '.$CI->lang->line((($days	> 1) ? 'date_days' : 'date_day')).', ';
			}

			$seconds -= $days * 86400;
		}

		$hours = floor($seconds / 3600);

		if ($days > 0 OR $hours > 0)
		{
			if ($hours > 0)
			{
				$str .= $hours.' '.$CI->lang->line((($hours	> 1) ? 'date_hours' : 'date_hour')).', ';
			}

			$seconds -= $hours * 3600;
		}

		$minutes = floor($seconds / 60);

		if ($days > 0 OR $hours > 0 OR $minutes > 0)
		{
			if ($minutes > 0)
			{
				$str .= $minutes.' '.$CI->lang->line((($minutes	> 1) ? 'date_minutes' : 'date_minute')).', ';
			}

			$seconds -= $minutes * 60;
		}

		if ($str == '')
		{
			$str .= $seconds.' '.$CI->lang->line((($seconds	> 1) ? 'date_seconds' : 'date_second')).', ';
		}

		return substr(trim($str), 0, -1);
	}

function format_to_mysql_date($datetime='')
{
  if (!preg_match("/^(\d{1,2})[.\- \/](\d{1,2})[.\- \/](\d{2}(\d{2})?)( (\d{1,2}):(\d{1,2})(:(\d{1,2}))?)?$/", $datetime, $date))
    return FALSE;

  $day = $date[1];
  $month = $date[2];
  $year = $date[3];
  $hour = (empty($date[6])) ? 0 : $date[6];
  $min = (empty($date[7])) ? 0 : $date[7];
  $sec = (empty($date[9])) ? 0 : $date[9];

  return "{$year}-{$month}-{$day}";

}

/* End of file MY_date_helper.php */
/* Location: ./system/application/helpers/MY_date_helper.php */