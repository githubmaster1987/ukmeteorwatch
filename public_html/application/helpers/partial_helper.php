<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Хелперы View
 * @author Alexander Makarov
 * @link http://rmcreative.ru/
 */


/*

Пример

articles/index
<ol class="articles">
<?php
foreach($articles as $article){
  echo partial('articles/article', $article);
}
?>
</ol>



*/






/**
 * Позволяет включить подшаблон с определёнными параметрами
 *
 * @param string $template
 * @param array $data
 * @return string
 */
function partial($template, $data = array()){
	$CI = &get_instance();
	return $CI->load->view($template, $data, true);
}