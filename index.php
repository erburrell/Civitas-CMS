<?php
	ini_set('display_errors', TRUE);
	error_reporting(E_ALL);

include_once('./library/default.config');
include_once('./library/connection.inc');
include_once('./library/member.inc');
include_once('./library/viewControllers.inc');


if ($_POST['register'] == 'register') {
	header("Location: http://localhost/~rburrell/Civitas-CMS/templates/registration.php");
}

if ($_GET['node'] == NULL) {
	echo ViewController::getView(1);
} else {
	echo ViewController::getView($_GET['node']);
}
?>

<!-- Everything Below Here is new and unverified!! -->

<?php

?>