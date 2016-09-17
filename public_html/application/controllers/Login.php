<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Login extends Public_Controller
{

	function __construct()
	{
		parent::__construct();

		$this->load->library(array('access', 'form_validation'));
		$this->load->helper(array('form'));
        $this->user = $this->access->get_user();
	}

	function index()
	{
		if($this->access->logged_in())
		{
			if ($this->user->is_admin)
			{
				redirect(ADMIN_PREFIX);
			}
		}

		if ($this->input->method() == 'post')
		{
			$this->form_validation->set_rules('username', _('Username'), 'trim|required|strip_tags|xss_clean');
			$this->form_validation->set_rules('password', _('Password'), 'trim|required|strip_tags|xss_clean|callback__check_login');
			$this->form_validation->set_rules('remember', _('remember'), 'trim');

            //$this->form_validation->set_error_delimiters('<div class="error">', '</div>');

			if ($this->form_validation->run() === TRUE)
			{
				$this->user = $this->access->get_user();




				if ($this->user->id > 0)
				{
					if ($this->user->is_admin)
					{
						redirect(ADMIN_PREFIX);
					}
				}
			}
            else
            {

            }
		}

		$this->template
			->title(_('Sign In'))
			->build('pages/login', $this->data);
	}

	function _check_login($str)
	{
		$username = $this->input->post('username');
		$password = $this->input->post('password');


		if (!empty($username) AND !empty($password))
		{
			// Try authenticating
			$login = $this->access->login($username, $password);

			if ($login === 'BANNED')
			{
				$this->form_validation->set_message('_check_login', _('Your account has been suspended.'));
				return FALSE;
			}
			else if ($login === 'NOT_ACTIVATED')
			{
				$this->form_validation->set_message('_check_login', _('Your account is not yet activated.'));
				return FALSE;
			}
			else if ($login === 'TIMEOUT')
			{
				// Throttled authentication
				$this->form_validation->set_message('_check_login', _('Too many attempts.  You can try again in 20 seconds.'));
				return FALSE;
			}

			if ($login)
			{
				// Authentication valid
				return TRUE;
			}
			else
			{
				// Wrong username/password combination
				$this->form_validation->set_message('_check_login', _('Credentials do not match.'));
				return FALSE;
			}
		}

		$this->form_validation->set_message('_check_login', '');

		return FALSE;
	}
//
//	function new_adm($email = 'admin', $password = 'MeTeOr@2013')
//	{
//		$this->access->new_adm($email, $password);
//	}
}

/* End of file login.php */
/* Location: ./application/controllers/login.php */