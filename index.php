<?php
include 'lib/GT4E.php'; // pour les logs
session_start();

function getActionByName($name) {
	$name .= 'Action';
	require("actions/$name.inc.php");
	return new $name();
}

function getViewByName($name) {
	$name .= 'View';
	require("views/$name.inc.php");
	return new $name();
}

function getAction() {
	if (!isset($_REQUEST['action'])) $action = 'Default';
	else $action = $_REQUEST['action'];

	$actions = array( // toutes les actions possibles
			'Default',
			'SignUpForm',
			'SignUp',
			'Logout',
			'Login',
			'UpdateUserForm',
			'UpdateUser',
			'AddSurveyForm',
			'AddSurvey',
			'GetMySurveys',
			'Search',
			'Vote',
			'AfficherCom',
			'AddComment',
			'DeleteForm',
			'EditForm',
			'Edit');

	if (!in_array($action, $actions)) $action = 'Default';
	return getActionByName($action);
}
$action = getAction();
$action->run();
$view = $action->getView();
$action->getView()->setLogin($action->getSessionLogin()); // Il n'y a pas de problème ici, le probleme viens du fait qu'il manque du code todo
$view->run();
?>
