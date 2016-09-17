<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Generate extends CI_Controller
{
	function __construct()
	{
		parent::__construct();

		if( !$this->input->is_cli_request() )
		{
			show_error('Permission denied');
		}

		$this->controller = $this->router->fetch_class();
		$this->method = $this->router->fetch_method();

		define('AUTO_COMPLETE_PATH', APPPATH.'../docs/ci_autocomplete.php');

	}

	function autocomplete()
	{
		$this->load->helper('file');

		$models = '';

		//foreach (array(BASEPATH.'core', BASEPATH.'database', BASEPATH.'libraries', APPPATH.'models') as $dir)
		foreach (array(APPPATH.'models') as $dir)
		{
			$files = get_dir_file_info($dir, $top_level_only = TRUE);

			foreach ($files as $k => $file)
			{
				$class = file_get_contents($file['server_path']);

				preg_match_all('@class (?P<classname>\w+) extends@is', $class, $matches, PREG_SET_ORDER);

				foreach ($matches as $key => $item)
				{
					if (isset($item['classname']))
					{
						$models .= ' * @property ';
						// * @property Goal_types_model $goal_types_model
						$models .= $item['classname'];

						$models .= ' $'.strtolower($item['classname']);
						$models .= "\n";
					}
				}
			}
		}

$data = "<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * @property CI_DB_query_builder \$db
 * @property CI_DB_forge \$dbforge
 * @property CI_Benchmark \$benchmark
 * @property CI_Calendar \$calendar
 * @property CI_Cart \$cart
 * @property CI_Config \$config
 * @property CI_Controller \$controller
 * @property CI_Email \$email
 * @property CI_Encrypt \$encrypt
 * @property CI_Exceptions \$exceptions
 * @property CI_Form_validation \$form_validation
 * @property CI_Ftp \$ftp
 * @property CI_Hooks \$hooks
 * @property CI_Image_lib \$image_lib
 * @property CI_Input \$input
 * @property CI_Lang \$lang
 * @property CI_Loader \$load
 * @property CI_Log \$log
 * @property CI_Model \$model
 * @property CI_Output \$output
 * @property CI_Pagination \$pagination
 * @property CI_Parser \$parser
 * @property CI_Profiler \$profiler
 * @property CI_Router \$router
 * @property CI_Session \$session
 * @property CI_Sha1 \$sha1
 * @property CI_Table \$table
 * @property CI_Trackback \$trackback
 * @property CI_Typography \$typography
 * @property CI_Unit_test \$unit_test
 * @property CI_Upload \$upload
 * @property CI_URI \$uri
 * @property CI_User_agent \$user_agent
 * @property CI_Validation \$validation
 * @property CI_Xmlrpc \$xmlrpc
 * @property CI_Xmlrpcs \$xmlrpcs
 * @property CI_Zip \$zip
 * @property CI_Javascript \$javascript
 * @property CI_Jquery \$jquery
 * @property CI_Utf8 \$utf8
 * @property CI_Security \$security
 *
 *
 * @property Access \$access
 * @property Loremipsumgenerator \$loremipsumgenerator
 * @property Postmark \$postmark
 * @property Taconite \$taconite
 * @property Template \$template
 *
 *
$models *
 */

class CI_Controller {};

/**
 * @property CI_DB_query_builder \$db
 * @property CI_DB_forge \$dbforge
 * @property CI_Config \$config
 * @property CI_Loader \$load
 * @property CI_Session \$session
*/

class CI_Model {};

";

		write_file(AUTO_COMPLETE_PATH, $data);
	}



	function model($db_table = NULL, $overwrite = 0)
	{
		$this->load->helper(array('file', 'inflector'));

		$rules = array();
		$field_names = array();

		if ($fields = $this->db->list_fields($db_table))
		{
			foreach ($fields as $k => $field)
			{

				$field_names[] = "'{$field}'";

				if ($field == 'id' )
				{
					continue;
				}

				$title = ucwords(str_replace('_', ' ', $field));

				if (strpos($field, '_id') !== FALSE)
				{
					$rules[] = "array('field' => '{$field}', 'label' => '{$title}', 'rules' => 'trim')";
				}
				else
				{
					$rules[] = "array('field' => '{$field}', 'label' => '{$title}', 'rules' => 'trim|required')";
				}
			}
		}

		$rules = implode("\n\t\t\t,", $rules);
		$field_names = implode("\n\t\t\t,", $field_names);


		if (!empty($db_table))
		{
			$file_name = ucfirst($db_table.'_model.php');
			$class_name = ucfirst($db_table.'_model');

			$primary_key_name = singular($db_table);

			$path = APPPATH.'models/';

			if(!file_exists($path.$file_name) || $overwrite == 1)
			{
$data =
"<?php defined('BASEPATH') OR exit('No direct script access allowed');

class {$class_name} extends MY_Model
{
	function __construct()
	{
		parent::__construct();

		\$this->table = '{$db_table}';
		\$this->primaryKey = '{$primary_key_name}_id';
		\$this->returnArray = FALSE;

		\$this->validation_rules = array(
			{$rules}
		);

		\$this->fields = array(
			{$field_names}
		);
	}
}

/* End of file {$file_name} */
/* Location: ./application/models/{$file_name} */
";
				write_file($path.$file_name, $data);
			}
			else
			{
				die("\nModel {$file_name} is already exist.\n");
			}
		}
		else
		{
			die("\nPlease provide database table name.\n");
		}

		$this->autocomplete();
	}

	function model_fields($db_table = NULL)
	{
		$this->load->helper(array('file', 'inflector'));

		if ($fields = $this->db->list_fields($db_table))
		{
			foreach ($fields as $k => $field)
			{

				$field_names[] = "'{$field}'";
			}
		}

		$field_names = implode("\n\t\t\t,", $field_names);


		if (!empty($db_table))
		{

$data =
"
		\$this->fields = array(
			{$field_names}
		);
";

			echo $data;
		}
		else
		{
			die("\nPlease provide database table name.\n");
		}

		$this->autocomplete();
	}

}
