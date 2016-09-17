<?php defined('BASEPATH') OR exit('No direct script access allowed');

class MY_Controller extends CI_Controller
{
	public $data;
	public $user;
	public $view = '';

	public function __construct()
	{
		parent::__construct();

		if ($this->input->cookie('adm') == 8351 && !$this->input->is_ajax_request())
		{
			$this->output->enable_profiler(TRUE);
		}

		$this->data = new stdClass();

		//$this->load->library(array('access'));
		//$this->controller = $this->router->class;
		//$this->method = $this->router->method;
		//$this->router->directory;
		//$this->user = $this->access->get_user();

		//		$model = strtolower($this->controller).'_model';
		//		$this->primary_key = $this->$model->primaryKey;
		//$this->plural_title = ucfirst(plural($this->controller));
		//$this->singular_title = ucfirst(singular($this->controller));
	}

	protected function restrict()
	{
		if ($this->access->logged_in())
		{
			if ($this->user->is_admin || $this->user->is_manager)
			{
				redirect(ADMIN_PREFIX);
			}
			elseif ($this->user->is_user)
			{
				redirect(USER_PREFIX);
			}
		}
		else
		{
			redirect('login');
		}
	}

	function redirect_to_login()
	{
		if ($this->input->is_ajax_request())
		{
			$this->taconite->set('eval', 'window.location.replace("' . site_url('login') . '");');
			$this->taconite->output();
			exit();
		}
		else
		{
			redirect('login');
		}
	}

}

class Admin_Controller extends MY_Controller
{
	public function __construct()
	{
		parent::__construct();

        $this->load->library(array('access'));
        $this->user = $this->access->get_user();

		if(!$this->user->is_admin)
		{
			$this->redirect_to_login();
		}

		$this->is_admin_area = TRUE;

		// Nope, just use the default layout
		if ($this->template->layout_exists('default'))
		{
			$this->template->set_layout('default');
		}

		$this->view = ADMIN_PREFIX . $this->router->class . '/' . $this->router->method;
		$this->uri_prefix = $this->view . '/';

		// Make sure whatever page the user loads it by, its telling search robots the correct formatted URL
		$this->template->set_metadata('canonical', site_url($this->uri->uri_string()), 'link');


		// Template configuration
		$this->template
			->enable_parser(FALSE)
			->set_partial('flashdata', 'partials/flashdata')
		;
	}

}

class Public_Controller extends MY_Controller
{

	public function __construct()
	{
		parent::__construct();

		// Nope, just use the default layout
		if ($this->template->layout_exists('default'))
		{
			$this->template->set_layout('default');
		}

		$this->view =  $this->router->class . '/' . $this->router->method;


		$this->uri_prefix = $this->view . '/';

		// Make sure whatever page the user loads it by, its telling search robots the correct formatted URL
		$this->template->set_metadata('canonical', site_url($this->uri->uri_string()), 'link');


		// Template configuration
		$this->template
			->enable_parser(FALSE)
		;
	}

}


/**
 * Returns the CI object.
 *
 * Example: ci()->db->get('table');
 *
 * @staticvar	object	$ci
 * @return		object
 */
function ci()
{
	static $CI;
	if (!$CI) $CI = get_instance();
	return $CI;
}

/* End of file MY_Controller.php */
/* Location: ./application/core/MY_Controller.php */