<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

function generate_password_hash($hash = NULL, $password = NULL)
{
	if (empty($password))
	{
		$password = get_random_password(6, 8, TRUE, TRUE);
	}

	return sha1(config_item('encryption_key') . $hash . $password);
}


// ------------------------------------------------------------------------
if ( ! function_exists('get_random_password'))
{
    /**
     * Generate a random password.
     *
     * get_random_password() will return a random password with length 6-8 of lowercase letters only.
     *
     * @access    public
     * @param    $chars_min the minimum length of password (optional, default 6)
     * @param    $chars_max the maximum length of password (optional, default 8)
     * @param    $use_upper_case boolean use upper case for letters, means stronger password (optional, default false)
     * @param    $include_numbers boolean include numbers, means stronger password (optional, default false)
     * @param    $include_special_chars include special characters, means stronger password (optional, default false)
     *
     * @return    string containing a random password
     */
    function get_random_password($chars_min=6, $chars_max=8, $use_upper_case=false, $include_numbers=false, $include_special_chars=false)
    {
        $length = rand($chars_min, $chars_max);
        $selection = 'aeuybcdfghjkmnpqrstvwxz';
        if($include_numbers) {
            $selection .= "1234567890";
        }
        if($include_special_chars) {
            $selection .= "!@c2ac6c09d91a8a2b2be78456fda8eb5f37ffba85quot;#$%&[]{}?|";
        }

        $password = "";
        for($i=0; $i<$length; $i++) {
            $current_letter = $use_upper_case ? (rand(0,1) ? strtoupper($selection[(rand() % strlen($selection))]) : $selection[(rand() % strlen($selection))]) : $selection[(rand() % strlen($selection))];
            $password .=  $current_letter;
        }

        return $password;
    }

}





/**
 * Check password strength
 *
 * @access	public
 * @param	string
 * @return	string
 */
if ( ! function_exists('check_strength'))
{
	function check_strength($password, $return_type = 2)
	{
		$_points = 0;
        $lower_case_count = 0;
        $upper_case_count = 0;
        $numeric_count = 0;
        $special_count = 0;
       	$password_length = strlen($password);

        if($password_length > 0)
        {
			// check the length of the password
			if ($password_length <= 4)
	        {
	            $_points += 5;
	        }
	        else
	        {
	            if($password_length > 4 AND $password_length <= 7)
	            {
	                $_points += 10;
	            }
	            else
	            {
	                $_points += 25;
	            }
	        }

	        // check each character
			for($i = 0; $i <= $password_length - 1; $i++)
	        {
	        	$character = $password[$i];

	        	// is it lower case?
	            if (ctype_lower($character))
	            {
	                $lower_case_count++;
	            }
	            // is it upper case?
	            else if (ctype_upper($character))
	            {
	                $upper_case_count++;
	            }
	            // is it a digit
	            else if (ctype_digit($character))
	            {
	                $numeric_count++;
	            }
				// punctuation character?
	            else if (ctype_punct($character))
	            {
	                $special_count++;
	            }
	        }

	        // give points depending on how many character of which kind we got

	        // lower AND upper case characters
			if($lower_case_count > 0 AND $upper_case_count > 0)
			{
				$_points += 25;
			}
			else
			{
				// lower OR upper case characters
		        if($upper_case_count > 0 OR $lower_case_count > 0)
		        {
		            $_points += 10;
		        }
			}

	        // numeric
	        if($numeric_count <= 3)
	        {
	           $_points += 10;
	        }
	        else
	        {
	        	if($numeric_count > 3)
	        	{
	        		$_points += 20;
	        	}
	        }

	        // special characters
            if ($special_count == 1)
            {
                $_points += 10;
            }
            else
            {
                $_points += 25;
            }

            // bonus
            if($numeric_count > 0 AND ($upper_case_count > 0 OR $lower_case_count > 0) AND $special_count == 0)
            {
            	$_points += 2;
            }
            else
            {
            	if($numeric_count > 0 AND $special_count > 0 AND (($upper_case_count > 0 AND $lower_case_count == 0) OR ($upper_case_count == 0 AND $lower_case_count > 0)))
            	{
            		$_points += 3;
            	}
            	else
            	{
            		if($numeric_count > 0 AND $special_count > 0 AND $upper_case_count > 0 AND $lower_case_count > 0)
            		{
            			$_points += 5;
            		}
            	}
            }
        }

		//result
		if($return_type == 1)
		{
			return $_points;
		}
		else
		{
			if($_points >= 90)
			{
				return "Very secure";
			}
			elseif ($_points >= 80)
			{
				return "Secure";
			}
			elseif ($_points >= 70)
			{
				return "Very strong";
			}
			elseif ($_points >= 60)
			{
				return "Strong";
			}
			elseif ($_points >= 50)
			{
				return "Average";
			}
			elseif ($_points >= 25)
			{
				return "Weak";
			}
			else
			{
				return "Very weak";
			}
		}
	}
}

if ( ! function_exists('generate_secure_password'))
{
	function generate_secure_password()
	{
		$_numbers = array("1", "2", "3", "4", "5", "6", "7", "8", "9");
		$_upper_case_letters = array("A","B","C","D","E","F","G","H","J","K","L","M","N","P","Q","R","S","T","U","V","W","X","Y","Z");
		$_lower_case_letters = array("a","b","c","d","e","f","g","h","j","k","m","n","o","p","q","r","s","t","u","v","w","x","y","z");
		$_characters = array("=", "+", "&", "!", "@", "$", "#", "*");

		$password_length = rand(8, 15);
		$number_of_characters = 2;
		$number_of_numbers = 3;
		$number_of_upper_case_letters = rand(1, $password_length - 6);
		$number_of_lower_case_letters = $password_length - (5 + $number_of_upper_case_letters);

		$password = "";
		$number_of_characters_used = 0;
		$number_of_numbers_used = 0;
		$number_of_upper_case_letters_used = 0;
		$number_of_lower_case_letters_used = 0;
		for($i = 1; $i <= $password_length; $i++)
		{
			$found = FALSE;
			$type = rand(1,4);
			switch($type)
			{
				case "1":
						//numbers
						if($number_of_numbers > $number_of_numbers_used)
						{
							$number_index = rand(0, count($_numbers)-1);
							$password .= $_numbers[$number_index];
							$found = TRUE;
							$number_of_numbers_used ++;
						}
					break;
				case "2":
						//characters
						if($number_of_characters > $number_of_characters_used)
						{
							$character_index = rand(0, count($_characters)-1);
							$password .= $_characters[$character_index];
							$found = TRUE;
							$number_of_characters_used ++;
						}
					break;
				case "3":
						//upper case
						if($number_of_upper_case_letters > $number_of_upper_case_letters_used)
						{
							$upper_case_index = rand(0, count($_upper_case_letters)-1);
							$password .= $_upper_case_letters[$upper_case_index];
							$found = TRUE;
							$number_of_upper_case_letters_used ++;
						}
					break;
				case "4":
						//lower case
						if($number_of_lower_case_letters > $number_of_lower_case_letters_used)
						{
							$lower_case_index = rand(0, count($_lower_case_letters)-1);
							$password .= $_lower_case_letters[$lower_case_index];
							$found = TRUE;
							$number_of_lower_case_letters_used ++;
						}
					break;
			}

			if($found == FALSE)
			{
				$type = rand(1,4);
				switch($type)
				{
					case "1":
							//numbers
							if($number_of_numbers > $number_of_numbers_used)
							{
								$number_index = rand(0, count($_numbers)-1);
								$password .= $_numbers[$number_index];
								$found = TRUE;
								$number_of_numbers_used ++;
							}
						break;
					case "2":
							//characters
							if($number_of_characters > $number_of_characters_used)
							{
								$character_index = rand(0, count($_characters)-1);
								$password .= $_characters[$character_index];
								$found = TRUE;
								$number_of_characters_used ++;
							}
						break;
					case "3":
							//upper case
							if($number_of_upper_case_letters > $number_of_upper_case_letters_used)
							{
								$upper_case_index = rand(0, count($_upper_case_letters)-1);
								$password .= $_upper_case_letters[$upper_case_index];
								$found = TRUE;
								$number_of_upper_case_letters_used ++;
							}
						break;
					case "4":
							//lower case
							if($number_of_lower_case_letters > $number_of_lower_case_letters_used)
							{
								$lower_case_index = rand(0, count($_lower_case_letters)-1);
								$password .= $_lower_case_letters[$lower_case_index];
								$found = TRUE;
								$number_of_lower_case_letters_used ++;
							}
						break;
				}
			}

			if($found == FALSE)
			{
				if($number_of_numbers > $number_of_numbers_used)
				{
					$number_index = rand(0, count($_numbers)-1);
					$password .= $_numbers[$number_index];
					$number_of_numbers_used ++;
				}
				else
				{
					if($number_of_characters > $number_of_characters_used)
					{
						$character_index = rand(0, count($_characters)-1);
						$password .= $_characters[$character_index];
						$number_of_characters_used ++;
					}
					else
					{
						if($number_of_upper_case_letters > $number_of_upper_case_letters_used)
						{
							$upper_case_index = rand(0, count($_upper_case_letters)-1);
							$password .= $_upper_case_letters[$upper_case_index];
							$number_of_upper_case_letters_used ++;
						}
						else
						{
							if($number_of_lower_case_letters > $number_of_lower_case_letters_used)
							{
								$lower_case_index = rand(0, count($_lower_case_letters)-1);
								$password .= $_lower_case_letters[$lower_case_index];
								$number_of_lower_case_letters_used ++;
							}
						}
					}
				}
			}
		}
		return $password;
	}
}

function pc_passwordcheck($user,$pass) {
$word_file = '/usr/share/dict/words';
$lc_pass = strtolower($pass);
// also check password with numbers or punctuation subbed for letters
$denum_pass = strtr($lc_pass,'5301!','seoll');
$lc_user = strtolower($user);
// the password must be at least six characters
if (strlen($pass) < 6) {
return 'The password is too short.';
}
// the password can't be the username (or reversed username)
if (($lc_pass == $lc_user) || ($lc_pass == strrev($lc_user)) ||
($denum_pass == $lc_user) || ($denum_pass == strrev($lc_user))) {
return 'The password is based on the username.';
}
// count how many lowercase, uppercase, and digits are in the password
$uc = 0; $lc = 0; $num = 0; $other = 0;
for ($i = 0, $j = strlen($pass); $i < $j; $i++) {
$c = substr($pass,$i,1);
if (preg_match('/^[[:upper:]]$/',$c)) {
$uc++;
} elseif (preg_match('/^[[:lower:]]$/',$c)) {
$lc++;
} elseif (preg_match('/^[[:digit:]]$/',$c)) {
$num++;
} else {
$other++;
}
}
// the password must have more than two characters of at least
// two different kinds
$max = $j - 2;
if ($uc > $max) {
return "The password has too many upper case characters.";
}
if ($lc > $max) {
return "The password has too many lower case characters.";
}
if ($num > $max) {
return "The password has too many numeral characters.";
}
if ($other > $max) {
return "The password has too many special characters.";
}
// the password must not contain a dictionary word
if (is_readable($word_file)) {
if ($fh = fopen($word_file,'r')) {
$found = false;
while (! ($found || feof($fh))) {
$word = preg_quote(trim(strtolower(fgets($fh,1024))),'/');
if (preg_match("/$word/",$lc_pass) ||
preg_match("/$word/",$denum_pass)) {
$found = true;
}
}
fclose($fh);
if ($found) {
return 'The password is based on a dictionary word.';
}
}
}
return false;
}


function checkPasswordStrength($password, $username = false) {
        $returns = array(
            'strength' => 0,
            'error'    => 0,
            'text'     => ''
        );

        $length = strlen($password);

        if ($length < 8) {
            $returns['error']    = 1;
            $returns['text']     = 'The password is not long enough';
        } else {

            //check for a couple of bad passwords:
            if ($username && strtolower($password) == strtolower($username)) {
                $returns['error']    = 4;
                $returns['text']     = 'Password cannot be the same as your Username';
            } elseif (strtolower($password) == 'password') {
                $returns['error']    = 3;
                $returns['text']     = 'Password is too common';
            } else {

                preg_match_all ("/(.)\1{2}/", $password, $matches);
                $consecutives = count($matches[0]);

                preg_match_all ("/\d/i", $password, $matches);
                $numbers = count($matches[0]);

                preg_match_all ("/[A-Z]/", $password, $matches);
                $uppers = count($matches[0]);

                preg_match_all ("/[^A-z0-9]/", $password, $matches);
                $others = count($matches[0]);

                //see if there are 3 consecutive chars (or more) and fail!
                if ($consecutives > 0) {
                    $returns['error']    = 2;
                    $returns['text']     = 'Too many consecutive characters';

                } elseif ($others > 1 || ($uppers > 1 && $numbers > 1)) {
                    //bulletproof
                    $returns['strength'] = 5;
                    $returns['text']     = 'Virtually Bulletproof';

                } elseif (($uppers > 0 && $numbers > 0) || $length > 14) {
                    //very strong
                    $returns['strength'] = 4;
                    $returns['text']     = 'Very Strong';

                } else if ($uppers > 0 || $numbers > 2 || $length > 9) {
                    //strong
                    $returns['strength'] = 3;
                    $returns['text']     = 'Strong';

                } else if ($numbers > 1) {
                    //fair
                    $returns['strength'] = 2;
                    $returns['text']     = 'Fair';

                } else {
                    //weak
                    $returns['strength'] = 1;
                    $returns['text']     = 'Weak';
                }
            }
        }
        return $returns;
    }

/* End of file password_helper.php */
/* Location: ./system/application/helpers/password_helper.php */