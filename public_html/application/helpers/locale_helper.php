<?php

function load_locale($locale) 
{
	$locale_folder = APPPATH.'locale/';

	$domain = 'default';

	// activate the locale setting
	$locale_set = setlocale(LC_ALL, $locale);
	$postfix = '';

	if ($locale_set == false) {
		$utf8_names  = array('utf8', 'UTF8', 'utf-8', 'UTF-8');
		foreach ($utf8_names as $utf8) {
			$postfix = '.'.$utf8;
			$locale_set = setlocale(LC_ALL, $locale.$postfix);

			if ($locale_set != false) {
				break;
			}
			else {
				$postfix = '';
			}
		}
	}

	putenv('LANG='.$locale.$postfix);

	// bind it
	bindtextdomain($domain, $locale_folder);
	// then activate it
	textdomain($domain);

	bind_textdomain_codeset($domain, "UTF-8");
}
