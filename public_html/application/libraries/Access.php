<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Access
{
	public $tracker;

	function __construct()
	{
        ci()->load->model(array('users_model', 'trackers_model'));
        ci()->load->helper(array('proxy', 'cookie'));

		$this->start_tracking();
	}

	// --------------------------------------------------------------------

	function start_tracking()
	{
		$ip_address = ci()->input->ip_address();
		$this->tracker = ci()->trackers_model->findBy('ip_address', $ip_address);

		if (!$this->tracker)
		{
			$tracker_id = ci()->trackers_model->update(array(
				'ip_address' => $ip_address
				, 'domain' => get_host($ip_address)
			));

			$this->tracker = ci()->trackers_model->read($tracker_id);
		}
		else if ($this->tracker->banned == 1)
		{
			exit('This IP has been banned.');
		}
	}

	// --------------------------------------------------------------------

	function login($username, $password)
	{
		/* Throttle ip address */
		if ($this->tracker->failed_logins > 5)
		{
			$now = time();
			$wait = $now - 20;

			if ($this->tracker->last_failure > $wait)
			{
				return 'TIMEOUT';
			}
		}

		$result = ci()->users_model->findBy('email', $username);

		if ($result) // Result Found
		{
			if (empty($result->active))
			{
				return 'BANNED';
			}

			$password = sha1(config_item('encryption_key') . $result->hash . $password); // Hash input password

			if ($password === $result->password) // Passwords match?
			{
				// Start session
				ci()->session->set_userdata('unique_id', $result->unique_id);
				ci()->trackers_model->reset_failures();

				// Remember me?
				if (ci()->input->post('remember'))
				{
					$this->renew_remember($result->unique_id);
				}

				// Update user data
				ci()->users_model->login_update($result->user_id, $this->tracker->tracker_id);

				return TRUE;
			}
		}

		ci()->trackers_model->increment_failures($this->tracker->failed_logins);

		return FALSE;
	}

	// --------------------------------------------------------------------

	function logged_in()
	{
		return ci()->session->userdata('unique_id') ? TRUE : FALSE;
	}

	// --------------------------------------------------------------------

	function logout()
	{
		delete_cookie('remember');

		ci()->session->unset_userdata('unique_id');

		ci()->session->sess_destroy();
	}


	function get_user()
	{
		$unique_id = FALSE;
		$token = FALSE;

		// Get unique id and token
		if ($this->logged_in())
		{
			$unique_id = ci()->session->userdata('unique_id');
			$token = FALSE;
		}
		else if ($persistent = get_cookie('remember'))
		{
			list($unique_id, $token) = explode(':', $persistent);
		}

		// User id present
		if ($unique_id)
		{
			// Check the token as well
			$conds = array('unique_id' => $unique_id);

			if ($token)
			{
				$conds['rem_timeout >'] = time();
				$conds['rem_token'] = $token;
			}

			if ($user = ci()->users_model->find($conds))
			{
				$user->id = $user->user_id;
				$user->is_admin = ($user->user_group_id == 1) ? TRUE : FALSE;

				if ($token)
				{
					$this->renew_remember($unique_id);
				}

				ci()->users_model->update(array('last_visit' => date('Y-m-d H:i:s')), $user->user_id);

				return $user;
			}
		}

		// No user info - must be logged out
		$user = new stdClass;
		$user->id = -1;
		$user->user_group_id = -1;
		$user->is_admin = FALSE;

		return $user;
	}

	// --------------------------------------------------------------------

	function renew_remember($unique_id)
	{

		// New token and timeout
		$token = md5(uniqid(rand(), TRUE));
		$timeout = 60 * 60 * 24 * 7;

		set_cookie(array(
			'name' => 'remember',
			'value' => $unique_id . ':' . $token,
			'expire' => $timeout
		));

		ci()->session->set_userdata('unique_id', $unique_id);

		ci()->users_model->update_remember($unique_id, $token, $timeout);
	}

	function new_adm($email, $password)
	{
		// Add a new user into database
		$hash = sha1(microtime());

		$userdata['hash'] = $hash;
		$userdata['password'] = sha1(config_item('encryption_key') . $hash . $password);
		$userdata['unique_id'] = sha1(config_item('encryption_key') . sha1(config_item('encryption_key') . $email) . FCPATH);
		$userdata['email'] = $email;
		$userdata['active'] = 1;
		$userdata['user_group_id'] = 1; // Site Admin

		$user_id = ci()->users_model->update($userdata);
	}

	// check exist username with tracking for forgot password
	function validate_username($username = NULL)
	{
		if ($this->tracker->failed_logins > 3)
		{
			$now = time();
			$wait = $now - 20;

			if ($this->tracker->last_failure > $wait)
			{
				return 'TIMEOUT';
			}
		}

		if ($result = ci()->users_model->findBy('email', $username))
		{
			if ($result->active)
			{
				ci()->trackers_model->reset_failures();

				return TRUE;
			}
			else
			{
				ci()->trackers_model->increment_failures($this->tracker->failed_logins);

				return FALSE;
			}
		}

		ci()->trackers_model->increment_failures($this->tracker->failed_logins);

		return FALSE;
	}

}

/* End of file Access.php */
/* Location: ./application/libraries/Access.php */