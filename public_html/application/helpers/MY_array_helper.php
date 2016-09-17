<?php

if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * CodeIgniter Array Helpers
 *
 * @package		CodeIgniter
 * @subpackage	Helpers
 * @category	Helpers
 * @link		http://www.codeigniter.com/user_guide/helpers/text_helper.html
 */
if (!function_exists('multi_array_key_exists'))
{

	function multi_array_key_exists($needle, $haystack)
	{

		foreach ($haystack as $key => $value) :

			if ($needle == $key) return true;

			if (is_array($value)) :
				if (multi_array_key_exists($needle, $value) == true) return true;
				else continue;
			endif;

		endforeach;

		return false;
	}

}



/*

  The sorted array is now in the return value of the function instead of being passed by reference.

  $data[] = array('volume' => 67, 'edition' => 2);
  $data[] = array('volume' => 86, 'edition' => 1);
  $data[] = array('volume' => 85, 'edition' => 6);
  $data[] = array('volume' => 98, 'edition' => 2);
  $data[] = array('volume' => 86, 'edition' => 6);
  $data[] = array('volume' => 67, 'edition' => 7);

  // Pass the array, followed by the column names and sort flags
  $sorted = array_orderby($data, 'volume', SORT_DESC, 'edition', SORT_ASC);
 */

function array_orderby()
{
	$args = func_get_args();
	$data = array_shift($args);
	foreach ($args as $n => $field)
	{
		if (is_string($field))
		{
			$tmp = array();
			foreach ($data as $key => $row) $tmp[$key] = $row[$field];
			$args[$n] = $tmp;
		}
	}
	$args[] = &$data;
	call_user_func_array('array_multisort', $args);
	return array_pop($args);
}

function natsort2d(&$arrIn, $index = null)
{

	$arrTemp = array();
	$arrOut = array();

	foreach ($arrIn as $key => $value)
	{

		reset($value);
		$arrTemp[$key] = is_null($index) ? current($value) : $value[$index];
	}

	natsort($arrTemp);

	foreach ($arrTemp as $key => $value)
	{
		$arrOut[$key] = $arrIn[$key];
	}

	$arrIn = $arrOut;
}

if (!function_exists('array_object_merge'))
{

	/**
	 * Merge an array or an object into another object
	 *
	 * @param object $object The object to act as host for the merge.
	 * @param object|array $array The object or the array to merge.
	 */
	function array_object_merge(&$object, $array)
	{
		// Make sure we are dealing with an array.
		is_array($array) OR $array = get_object_vars($array);

		foreach ($array as $key => $value)
		{
			$object->{$key} = $value;
		}
	}

}

if (!function_exists('array_for_select'))
{

	/**
	 * @todo Document this please.
	 *
	 * @return boolean
	 */
	function array_for_select()
	{
		$args = func_get_args();

		$return = array();

		switch (count($args))
		{
			case 3:
				foreach ($args[0] as $itteration):
					if (is_object($itteration)) $itteration = (array) $itteration;
					$return[$itteration[$args[1]]] = $itteration[$args[2]];
				endforeach;
				break;

			case 2:
				foreach ($args[0] as $key => $itteration):
					if (is_object($itteration)) $itteration = (array) $itteration;
					$return[$key] = $itteration[$args[1]];
				endforeach;
				break;

			case 1:
				foreach ($args[0] as $itteration):
					$return[$itteration] = $itteration;
				endforeach;
				break;

			default:
				return false;
		}

		return $return;
	}

}

if (!function_exists('html_to_assoc'))
{

	/**
	 * @todo Document this please.
	 *
	 * @param array $html_array
	 * @return array
	 */
	function html_to_assoc($html_array)
	{
		$keys = array_keys($html_array);

		if (!isset($keys[0]))
		{
			return array();
		}

		$total = count(current($html_array));

		$array = array();

		for ($i = 0; $i < $total; $i++)
		{
			foreach ($keys as $key)
			{
				$array[$i][$key] = $html_array[$key][$i];
			}
		}

		return $array;
	}

}

if (!function_exists('assoc_array_prop'))
{

	/**
	 * Associative array property
	 *
	 * Reindexes an array using a property of your elements. The elements should
	 * be a collection of array or objects.
	 *
	 * Note: To give a full result all elements must have the property defined
	 * in the second parameter of this function.
	 *
	 * @author Marcos Coelho
	 * @param array $arr
	 * @param string $prop Should be a common property with value scalar, as id, slug, order.
	 * @return array
	 */
	function assoc_array_prop(array &$arr = null, $prop = 'id', $multi_keys = FALSE)
	{
		$newarr = array();

		foreach ($arr as $old_index => $element)
		{
			if (is_array($element))
			{
				if (isset($element[$prop]) && is_scalar($element[$prop]))
				{
					if ($multi_keys)
					{
						$newarr[$element[$prop]][] = $element;
					}
					else
					{
						$newarr[$element[$prop]] = $element;
					}
				}
			}
			elseif (is_object($element))
			{
				if (isset($element->{$prop}) && is_scalar($element->{$prop}))
				{
					if ($multi_keys)
					{
						$newarr[$element->{$prop}][] = $element;
					}
					else
					{
						$newarr[$element->{$prop}] = $element;
					}
				}
			}
		}

		return $arr = $newarr;
	}

}

// --------------------------------------------------------------------

/**
 * Generate CSV from a query result object
 *
 * @access	public
 * @param	object	The query result object
 * @param	string	The delimiter - comma by default
 * @param	string	The newline character - \n by default
 * @param	string	The enclosure - double quote by default
 * @return	string
 */
function csv_from_array($list, $delim = ",", $newline = "\r\n", $enclosure = '"')
{
	$out = '';

	// Next blast through the result array and build out the rows
	foreach ($list as $row)
	{
		if (is_array($row))
		{
			foreach ($row as $item)
			{
				$out .= $enclosure . str_replace($enclosure, $enclosure . $enclosure, $item) . $enclosure . $delim;
			}
		}

		$out = rtrim($out);
		$out = rtrim($out, ',');
		$out .= $newline;
	}

	return $out;
}

function get_first_key($array)
{
	reset($array);

	return key($array);
}

function get_last_key($array)
{
	end($array);

	return key($array);
}

function get_second_key($array)
{
	reset($array);
	next($array);

	return key($array);
}

function unique_randoms($min, $max, $count)
{
	$numbers = array();

	while (count($numbers) < $count)
	{
		do
		{
			$test = mt_rand($min, $max);
		}
		while (in_array($test, $numbers));

		$numbers[] = $test;
	}

	return $numbers;
}

if (!function_exists('in_array_r'))
{

	/**
	 * Recursively search an array
	 * This method was copied and pasted from this URL (http://stackoverflow.com/questions/4128323/in-array-and-multidimensional-array)
	 * Real credit goes to (http://stackoverflow.com/users/427328/elusive)
	 *
	 * @author Elusive / Brennon Loveless
	 * @param string $needle the term being recursively searched for
	 * @param array $haystack multidimensional array to search
	 * @param boolean $strict use strict comparison or not
	 */
	function in_array_r($needle, $haystack, $strict = false)
	{
		foreach ($haystack as $item)
		{
			if (($strict ? $item === $needle : $item == $needle) || (is_array($item) && in_array_r($needle, $item, $strict)))
			{
				return true;
			}
		}
		return false;
	}

}



if (!function_exists('implode_wrap'))
{

	function implode_wrap($array, $glue = "\n", $before = '', $after = '', $delimiter = '|')
	{
		if (is_string($array))
		{
			$array = explode($delimiter, $array);
		}

		$output = '';

		foreach ($array as $item)
		{
			$output .= $before . $item . $after . $glue;
		}

		return substr($output, 0, -strlen($glue));
	}

}

if (!function_exists('span_wrap'))
{

	function span_wrap($array, $glue = "\n", $before = '<span>', $after = '</span>', $delimiter = '|')
	{
		return implode_wrap($array, "\n", '<span>', '</span>');
	}

}

/**
 * Array Helpers
 *
 * Provides additional functions for working with arrays.
 *
 * @package    Bonfire
 * @subpackage Helpers
 * @category   Helpers
 * @author     Bonfire Dev Team
 * @link       http://guides.cibonfire.com/helpers/array_helpers.html
 *
 */

if ( ! function_exists('array_index_by_key'))
{

	/**
	 * When given an array of arrays (or objects) it will return the index of the
	 * sub-array where $key == $value.
	 *
	 * <code>
	 * $array = array(
	 *	array('value' => 1),
	 *	array('value' => 2),
	 * );
	 *
	 * // Returns 1
	 * array_index_by_key('value', 2, $array);
	 * </code>
	 *
	 * @param $key mixed The key to search on.
	 * @param $value The value the key should be
	 * @param $array array The array to search through
	 * @param $identical boolean Whether to perform a strict type-checked comparison
	 *
	 * @return false|int An INT that is the index of the sub-array, or false.
	 */
	function array_index_by_key($key=null, $value=null, $array=null, $identical=false)
	{
		if (empty($key) || empty($value) || !is_array($array))
		{
			return false;
		}

		foreach ($array as $index => $sub_array)
		{
			$sub_array = (array)$sub_array;

			if (array_key_exists($key, $sub_array))
			{
				if ($identical)
				{
					if ($sub_array[$key] === $value)
					{
						return $index;
					}
				}
				else
				{
					if ($sub_array[$key] == $value)
					{
						return $index;
					}
				}
			}
		}//end foreach

		return FALSE;
	}//end array_index_by_key()
}

if (!function_exists('array_multi_sort_by_column'))
{
	/**
	 * Sort a multi-dimensional array by a column in the sub array
	 *
	 * @param array  $arr Array to sort
	 * @param string $col The name of the column to sort by
	 * @param int    $dir The sort directtion SORT_ASC or SORT_DESC
	 *
	 * @return void
	 */
	function array_multi_sort_by_column(&$arr, $col, $dir = SORT_ASC)
	{
		if (empty($col) || !is_array($arr))
		{
			return false;
		}

		$sort_col = array();
		foreach ($arr as $key => $row) {
			$sort_col[$key] = $row[$col];
		}

		array_multisort($sort_col, $dir, $arr);
	}//end array_multi_sort_by_column()
}

function shuffle_assoc($list) {
  if (!is_array($list)) return $list;

  $keys = array_keys($list);
  shuffle($keys);
  $random = array();
  foreach ($keys as $key)
    $random[$key] = $list[$key];

  return $random;
} 
