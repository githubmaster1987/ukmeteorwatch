<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
* Dbvars Library
* Simplifies storing variables in database, for example configuration variables.
*
* You must create table first.
**/
/*

CREATE TABLE IF NOT EXISTS `config` (
  `key` varchar(255) NOT NULL,
  `value` text NOT NULL,
  PRIMARY KEY  (`key`)
)

*/

/*
 * Описание по работе с объектами - http://ua2.php.net/manual/en/language.oop5.overloading.php
 */
/**
* Use:
*     $this->load->database();
*     $this->load->library('dbvars');
*
* To set value: $this->db_config->key = 'value';
* To get value:    $this->db_config->key
* To check if the variable isset: $this->db_config->__isset($key);
* To unset variable use: $this->db_config->__unset($key);
* As of PHP 5.1.0 You can use isset($this->db_config->key), unset($this->db_config->key);
*
* @version: 0.1 (c) _andrew 27-03-2008
**/
class Db_config {
    const TABLE = 'config';
    //Table where variables will be stored.

    private $data;
    private $ci;

    function __construct()
    {
        $this->ci =& get_instance();

        $q = $this->ci->db->get(self::TABLE);
        foreach ($q->result() as $row)
           {
               $this->data[$row->key] = unserialize($row->value);
           }
           $q->free_result();

    }

    function __get($key){
        return $this->data[$key];
    }

    function __set($key, $value){
        if (isset($this->data[$key])){
            $this->ci->db->where('key', $key);
            $this->ci->db->update(self::TABLE, array('value' => serialize($value)));
        } else {
            // TODO: database name protection improve
            $this->ci->db->insert(self::TABLE, array('`key`' => $key, '`value`' => serialize($value)));
        }
        $this->data[$key] = $value;
    }

    /**  As of PHP 5.1.0  */
    function __isset($key) {
        return isset($this->data[$key]);
    }

    /**  As of PHP 5.1.0  */
    function __unset($key) {
        $this->ci->db->delete(self::TABLE, array('key' => $key));
        unset($this->data[$key]);
    }
}
