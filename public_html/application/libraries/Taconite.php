<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

// --------------------------------------------------------------------
class Taconite {

	var $storage =  array();

// --------------------------------------------------------------------
	function Taconite()
	{
		ci()->load->helper('xml');

		log_message('ALL','Taconite library started');
	}

// --------------------------------------------------------------------
	function set($name = '', $attributes = NULL, $content = NULL)
	{
		if( is_string($attributes) and $attributes != '' )
		{
			switch(true)
			{
				/* self closing + css/class etc. etc. */
				case( is_array($content) ):

					foreach( $content as $arg1 => $arg2 )
					{
						$this->storage[] = '<'.$name.' select="'.$attributes.'" arg1="'.$arg1.'" arg2="'.$arg2.'" />';
					}
					break;

				/* single + show or class */
				case( $content == 'fast'):
				case( $content == 'slow' ):
				case( $name == 'addClass' ):
				case( $name == 'removeClass' ):
				case( $name == 'toggleClass' ):
					$this->storage[] = '<'.$name.' select="'.$attributes.'" arg1="'.$content.'" />';
					break;
				/* self closing + special: eval */
				case( ($name == 'eval' ) ):
					$attributes = trim(preg_replace('/\s+/', ' ', $attributes));

					$this->storage[] = '<eval><![CDATA['.process_data_jmr1($attributes).']]></eval>';

					break;
				/* content replace etc. etc. */
				case( is_string($content) ):

					$content = trim(preg_replace('/\s+/', ' ', $content));
					$this->storage[] = '<'.$name.' select="'.$attributes.'">'.process_data_jmr1($content). PHP_EOL .'</'.$name.'>';
					break;

				default:
					$this->storage[] = '<'.$name.' select="'.$attributes.'" arg1="'.process_data_jmr1($content).'" />';
			}
		}
	}

	function output()
	{
		// Don't want to cache this content
		ci()->output->set_header('Expires: Sat, 01 Jan 2000 00:00:01 GMT');
		ci()->output->set_header('Cache-Control: no-store, no-cache, must-revalidate');
		ci()->output->set_header('Cache-Control: post-check=0, pre-check=0, max-age=0');
		ci()->output->set_header('Last-Modified: ' . gmdate( 'D, d M Y H:i:s' ) . ' GMT' );

		ci()->output->set_header("Content-type: text/xml;charset=utf-8");

		$storage = $this->storage;

		if($storage != '' and is_array($storage) and count($storage) > 0 )
		{
			$xmlString = '';

			foreach( $storage as $string )
			{
				$xmlString .= PHP_EOL . $string;
			}

			echo '<taconite>'.$xmlString.PHP_EOL.'</taconite>';
		}
	}


}
