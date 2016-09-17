<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class ck_helper
{

	function  __construct()
	{
		if (session_id() === "") { session_start(); }
	}

	// -----------------------------------------------------------------

	/**
	 * Save the required session vars for CK Finder
	 */
	public function save_session_vars()
	{
		// basics
		$_SESSION['ck']['user_group'] = ci()->user->user_group_id;
		$_SESSION['ck']['integration_path'] = APPPATH.'libraries/';
		//$_SESSION['ck']['url'] = config_item('asset_url').config_item('asset_js_dir').'/backend/ckfinder/';
		$_SESSION['ck']['url'] = base_url().'uploads/blog/';
		$_SESSION['ck']['directory'] = FCPATH.'uploads/blog/';

		$_SESSION['ck']['language'] = 'en';
	}

	// -----------------------------------------------------------------
	/**
	 * From CK Finder
	 *
	 * @param int $imageWidth
	 * @param int $imageHeight
	 * @param int $imageBits
	 * @param int $imageChannels
	 * @return boolean
	 */
	public function setMemoryForImage($imageWidth, $imageHeight, $imageBits, $imageChannels)  {

		$MB = 1048576;  // number of bytes in 1M
        $K64 = 65536;    // number of bytes in 64K
        $TWEAKFACTOR = 2.4;  // Or whatever works for you
        $memoryNeeded = round( ( $imageWidth * $imageHeight
        * $imageBits
        * $imageChannels / 8
        + $K64
        ) * $TWEAKFACTOR
        ) + 3*$MB;

        //ini_get('memory_limit') only works if compiled with "--enable-memory-limit" also
        //Default memory limit is 8MB so well stick with that.
        //To find out what yours is, view your php.ini file.
        $memoryLimit = $this->returnBytes(@ini_get('memory_limit'))/$MB;
        if (!$memoryLimit) {
            $memoryLimit = 8;
        }

        $memoryLimitMB = $memoryLimit * $MB;
        if (function_exists('memory_get_usage')) {
            if (memory_get_usage() + $memoryNeeded > $memoryLimitMB) {
                $newLimit = $memoryLimit + ceil( ( memory_get_usage()
                + $memoryNeeded
                - $memoryLimitMB
                ) / $MB
                );
                if (@ini_set( 'memory_limit', $newLimit . 'M' ) === false) {
                    return false;
                }
            }
        } else {
            if ($memoryNeeded + 3*$MB > $memoryLimitMB) {
                $newLimit = $memoryLimit + ceil(( 3*$MB
                + $memoryNeeded
                - $memoryLimitMB
                ) / $MB
                );
                if (false === @ini_set( 'memory_limit', $newLimit . 'M' )) {
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * convert shorthand php.ini notation into bytes, much like how the PHP source does it
     * @link http://pl.php.net/manual/en/function.ini-get.php
     *
     * @static
     * @access public
     * @param string $val
     * @return int
     */
    public function returnBytes($val) {
        $val = trim($val);
        if (!$val) {
            return 0;
        }
        $last = strtolower($val[strlen($val)-1]);
        switch($last) {
            // The 'G' modifier is available since PHP 5.1.0
            case 'g':
                $val *= 1024;
            case 'm':
                $val *= 1024;
            case 'k':
                $val *= 1024;
        }

        return $val;
    }

}