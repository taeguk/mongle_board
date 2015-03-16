<?php
require_once 'db_config.php';
require_once 'security_fuctions.php';
require_once 'user_class.php';

class Session {
	var $session_name = 'mongle_board';
	var $login_user;
	var $login_string;
	
	function __construct() {
		$this->sec_session_start();
	}
	
	function sec_session_start() {
		$session_name = $this->session_name; // Set a custom session name
		$secure = SECURE;
		// This stops JavaScript being able to access the session id.
		$httponly = true;
		// Forces sessions to only use cookies.
		if (ini_set ( 'session.use_only_cookies', 1 ) === FALSE) {
			return false;
		}
		// Gets current cookies params.
		$cookieParams = session_get_cookie_params ();
		session_set_cookie_params ( $cookieParams ["lifetime"], $cookieParams ["path"], $cookieParams ["domain"], $secure, $httponly );
		// Sets the session name to the one set above.
		session_name ( $session_name );
		session_start (); // Start the PHP session
		session_regenerate_id ( true ); // regenerated the session, delete the old one.
		return true;
	}
	
	function _load_val_from_session() {
		if(!empty($_SESSION ['login_user']))
			$this->login_user = unserialize ( $_SESSION ['login_user'] );
		else
			$this->login_user = null;
		if(!empty($_SESSION ['login_string']))
			$this->login_string = $_SESSION['login_string'];
		else
			$this->login_string = null;
	}
	
	function login($user_id, $user_pw, $mysqli) {
		$loginUser = new User ();
		
		// Using prepared statements means that SQL injection is not possible.
		if ($stmt = $mysqli->prepare ( "SELECT ul_id, user_id, user_pw, pw_salt,
		stdnt_id, user_name, user_nickname,
		visit_cnt, signin_ts, signup_ts
        FROM user_list;
		WHERE user_id = ?
        LIMIT 1" )) {
			$stmt->bind_param ( 's', $user_id ); // Bind "$email" to parameter.
			$stmt->execute (); // Execute the prepared query.
			$stmt->store_result ();
			
			// get variables from result.
			$stmt->bind_result ( $loginUser->ul_id, $loginUser->user_id, 
					$db_user_pw, $db_pw_salt, $loginUser->user_name, $loginUser->user_nickname, 
					$loginUser->visit_cnt, $loginUser->signin_ts, $loginUser->signup_ts );
			$stmt->fetch ();
			
			// hash the password with the unique salt.
			$password = hash ( 'sha512', $user_pw . $db_pw_salt );
			if ($stmt->num_rows == 1) {
				// If the user exists we check if the account is locked
				// from too many login attempts
				
				if (checkbrute ( $loginUser->ul_id, $mysqli ) == true) {
					// Account is locked
					// Send an email to user saying their account is locked
					return false;
				} else {
					// Check if the password in the database matches
					// the password the user submitted.
					if ($db_user_pw == $user_pw) {
						// Password is correct!
						// Get the user-agent string of the user.
						$user_browser = $_SERVER ['HTTP_USER_AGENT'];
						// XSS protection
						$login_user->_init_val_with_xss_clean ();
						$_SESSION ['login_user'] = serialize ( $login_user );
						$this->login_user = $login_user;
						$_SESSION ['login_string'] = hash ( 'sha512', $user_pw . $user_browser );
						$this->login_string = $_SESSION['login_string'];
						// Login successful.
						return true;
					} else {
						// Password is not correct
						// We record this attempt in the database
						$now = time ();
						$mysqli->query ( "INSERT INTO login_attempts(user_id, time)
	        					VALUES ('$user_id', '$now')" );
						return false;
					}
				}
			} else {
				// No user exists.
				return false;
			}
		}
		return false;
	}
	function checkbrute($ul_id, $mysqli) {
		// Get timestamp of current time
		$now = time ();
		
		// All login attempts are counted from the past 2 hours.
		$valid_attempts = $now - (2 * 60 * 60);
		
		if ($stmt = $mysqli->prepare ( "SELECT time
				FROM login_attempts
				WHERE user_id = ?
				AND time > '$valid_attempts'" )) {
			$stmt->bind_param ( 'i', $ul_id );
			
			// Execute the prepared query.
			$stmt->execute ();
			$stmt->store_result ();
			
			// If there have been more than 5 failed logins
			if ($stmt->num_rows > 5) {
				return true;
			} else {
				return false;
			}
		}
		return false;
	}
	function login_check($mysqli) {
		// Check if all session variables are set
		if (!empty($this->login_user) && !empty($this->login_string)) {
			
			$login_user = $this->login_user;
			$login_string = $this->login_string;
			
			// Get the user-agent string of the user.
			$user_browser = $_SERVER ['HTTP_USER_AGENT'];
			
			if ($stmt = $mysqli->prepare ( "SELECT user_pw
                                      FROM user_list
                                      WHERE ul_id = ? LIMIT 1" )) {
				// Bind "$user_id" to parameter.
				$stmt->bind_param ( 'i', $login_user->ul_id );
				$stmt->execute (); // Execute the prepared query.
				$stmt->store_result ();
				
				if ($stmt->num_rows == 1) {
					// If the user exists get variables from result.
					$stmt->bind_result ( $db_user_pw );
					$stmt->fetch ();
					$login_check = hash ( 'sha512', $db_user_pw . $user_browser );
					
					if ($login_check == $login_string) {
						// Logged In!!!!
						return true;
					} else {
						// Not logged in
						return false;
					}
				} else {
					// Not logged in
					return false;
				}
			} else {
				// Not logged in
				return false;
			}
		} else {
			// Not logged in
			return false;
		}
	}
	function logout() {
		// Unset all session values
		$_SESSION = array();
		
		// get session parameters
		$params = session_get_cookie_params();
		
		// Delete the actual cookie.
		setcookie(session_name(),
				'', time() - 42000,
				$params["path"],
				$params["domain"],
				$params["secure"],
				$params["httponly"]);
		
		// Destroy session
		session_destroy();
	}
}
?>