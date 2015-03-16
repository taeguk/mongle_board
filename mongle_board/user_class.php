<?php
require_once 'security_functions.php';
class User {
	var $ul_id;
	var $user_id;
	var $user_name;
	var $user_name_print;
	var $user_nickname;
	var $user_nickname_print;
	var $visit_cnt;
	var $signin_ts;
	var $signup_ts;
	
	// init variable for html printing with xss protection fuction(=xss_clean)
	// xss_clean not work. this function must be modified!!!@@@@@@@@@@@@@@@@@@
	function _init_val_with_xss_clean() {
		$this->user_name_print = xss_clean($this->user_name);
		$this->user_nickname_print = xss_clean($this->user_nickname);
	}
}
?>