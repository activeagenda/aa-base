<?php
DEFINE('EXEC_STATE', 1);

//general settings
require_once '../config.php';
set_include_path(PEAR_PATH . PATH_SEPARATOR . get_include_path());

/**
 * Class file includes.
 */
require_once CLASSES_PATH . '/user.class.php';


/**
 * Utility functions includes. 
 */
require_once INCLUDE_PATH . '/general_util.php';
require_once INCLUDE_PATH . '/web_util.php';


/**
 * Sets custom error handler
 */
set_error_handler('handleError');


/**
 * Use a unique session name instead of PHPSESSID
 */
session_name(SITE_SESSIONNAME);
/**
 * Set time stamp for performance monitoring
 */
setTimeStamp('keep_alive');

/**
 * Starts session handling 
 */
if (! session_start()) {
    trigger_error("Session could not be created.", E_USER_ERROR);
}

/**
 * Get our user object from session.
 */
if(isset($_SESSION['User'])){
    $User = $_SESSION['User'];
} else {
    $User = null;
}

if (empty($User) || intval($_SESSION['Timeout']) < time()) {
	echo 0;
} else {
	//Update session timeout.
	$User->advanceSessionTimeout();
	echo 1;
}
?>