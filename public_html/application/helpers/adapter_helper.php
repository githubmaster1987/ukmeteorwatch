<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * @TODO: explain what it does
 * 
 * @access public
 * @param array  $record  An array of object with the data
 * @param string $key     The name of the field that will be the array key
 * @param string $value   The name of the field that will be the array value
 * @param array  $default Previous/Default value
 */
function records_to_assoc($record, $key = 'id', $value = 'name', $default = array())
{

	if ( ! is_array($default))
	{
			$default = array('' => $default);
	}

	$assoc = $default;
	
	foreach ($record as $row)
	{
		if (is_array($row)) {
			$assoc[$row[$key]] = $row[$value];
		} else {
			$assoc[$row->$key] = $row->$value;
		}
	}
	
	return $assoc;
}


    function find_element($from, $needle = '')
    {
        //echo $needle;
        foreach ($from as $k => $v) {
            if (mb_stripos($v, $needle, 0, 'UTF-8') !== FALSE)
            {
                return $k;
                break;
            }
        }
        return FALSE;
    }

    function find_array_element($from, $needle = '')
    {
        //echo $needle;
        foreach ($from as $k => $v) {
            if (stripos($v, $needle) !== FALSE)
            {
                return $k;
                break;
            }
        }
        return FALSE;
    }

    /*
     * Позволяет вывести значение по умолчанию, если переменная пуста или не выводить ничего.
     */
    function ife($var, $default='') {
      if (!empty($var)){
        return $var;
      }
      else{
        return $default;
      }
    }


    /*
     * function to determine if a variable represents a whole number:
     *
     * Usage
     * is_whole_number(2.00000000001); will return false
     * is_whole_number(2.00000000000); will return true
     */
    function is_whole_number($var){
        return (is_numeric($var)&&(intval($var)==floatval($var)));
    }

    /*
     * This is a function to sort an indexed 2D array by a specified sub array key, either ascending or descending.
     */
    function record_sort($records, $field, $reverse = FALSE) {

        $hash = array();

        foreach($records as $record) {

            $hash[$record[$field]] = $record;
        }

        ($reverse)? krsort($hash) : ksort($hash);

        $records = array();

        foreach($hash as $record) {

            $records []= $record;
        }

        return $records;
    }
    
/* End of file adapter_helper.php */
/* Location: ./application/helpers/adapter_helper.php */