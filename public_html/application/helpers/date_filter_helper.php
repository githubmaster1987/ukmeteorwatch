	<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

	if (!function_exists('create_date_filter')) 
	{
		// Quick Filter:  today, yesterday, this week, last week, last month, last 3 month, this year, last year
		function create_date_filter($path = '', $is_short = FALSE)
		{
			$filter = '<ul class="date_filter">';
			$filter .= '<li><strong>Quick date range:</strong></li>';

			// Now
			$timestamp = strtotime('now');
			$now  = date('Y-m-d', $timestamp);
			$year  = date('Y', $timestamp);
			$timestamp = $now;
			$filter .= '<li><a href="' . $path. '/date_from/' . $timestamp . '/date_to/' . $timestamp . '">Today</a></li>';

			// Yesterday
			$timestamp = strtotime('yesterday');
			$timestamp  = date('Y-m-d', $timestamp);
			$filter .= '<li><a href="' . $path. '/date_from/' . $timestamp . '/date_to/' . $timestamp . '">Yesterday</a></li>';

			// This week
			$timestamp = strtotime('last Monday');
			$timestamp  = date('Y-m-d', $timestamp);
			$filter .= '<li><a href="' . $path. '/date_from/' . $timestamp . '/date_to/' . $now . '">This week</a></li>';

			// Last week
			$timestamp = strtotime('Monday last week');
			$timestamp  = date('Y-m-d', $timestamp);
			$timestamp_2 = strtotime('Sunday last week');
			$timestamp_2  = date('Y-m-d', $timestamp_2);
			$filter .= '<li><a href="' . $path. '/date_from/' . $timestamp . '/date_to/' . $timestamp_2 . '">Last week</a></li>';

			// This month
			$timestamp = strtotime('first day of this month');
			$timestamp  = date('Y-m-d', $timestamp);
			$timestamp_2 = strtotime('now');
			$timestamp_2  = date('Y-m-d', $timestamp_2);
			$filter .= '<li><a href="' . $path. '/date_from/' . $timestamp . '/date_to/' . $timestamp_2 . '">This month</a></li>';
			
			// Last month
			$timestamp = strtotime('first day of -1 month');
			$timestamp  = date('Y-m-d', $timestamp);
			$timestamp_2 = strtotime('last day of -1 month');
			$timestamp_2  = date('Y-m-d', $timestamp_2);
			$filter .= '<li><a href="' . $path. '/date_from/' . $timestamp . '/date_to/' . $timestamp_2 . '">Last month</a></li>';

			if ( ! $is_short )
			{
				// Last 3 months
				$timestamp = strtotime('first day of -3 month');
				$timestamp  = date('Y-m-d', $timestamp);
				$timestamp_2 = strtotime('last day of -1 month');
				$timestamp_2  = date('Y-m-d', $timestamp_2);
				$filter .= '<li><a href="' . $path. '/date_from/' . $timestamp . '/date_to/' . $timestamp_2 . '">Last 3 months</a></li>';

				// This year
				$timestamp = strtotime('first day of January ' . $year);
				$timestamp  = date('Y-m-d', $timestamp);
				$filter .= '<li><a href="' . $path. '/date_from/' . $timestamp . '/date_to/' . $now . '">This year</a></li>';

				// Last year
				$timestamp = strtotime('first day of January ' . ($year - 1));
				$timestamp  = date('Y-m-d', $timestamp);
				$timestamp_2 = strtotime('last day of December ' . ($year - 1));
				$timestamp_2  = date('Y-m-d', $timestamp_2);
				$filter .= '<li><a href="' . $path. '/date_from/' . $timestamp . '/date_to/' . $timestamp_2 . '">Last  year</a></li>';

			}
			
			$filter .= '</ul>';
			
			return $filter;
		}
	}