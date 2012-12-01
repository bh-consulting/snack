<?
/**
* 
*/
class Utils
{
	public static function isMAC($string)
	{
		return preg_match('/^(?:[[:xdigit:]]{2}([-:]?))(?:[[:xdigit:]]{2}\1){4}[[:xdigit:]]{2}$/', $string);
	}

	public static function isIP($string)
	{
		return preg_match('/^([[:digit:]]{1,3}.){3}[[:digit:]]{1,3}(\/[[:digit:]]{2})?$/', $string);
	}
		
	public function restart_radius(){}

	public function generate_certificate($username){}

	public function delete_certificate($username){}

	public function add_cisco_user($login, $password){}
}
?>