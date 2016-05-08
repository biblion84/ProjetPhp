<?php

require_once("actions/Action.inc.php");

class LoginAction extends Action {

	/**
	 * Traite les données envoyées par le visiteur via le formulaire de connexion
	 * (variables $_POST['nickname'] et $_POST['password']).
	 * Le mot de passe est vérifié en utilisant les méthodes de la classe Database.
	 * Si le mot de passe n'est pas correct, on affiche le message "Pseudo ou mot de passe incorrect."
	 * Si la vérification est réussie, le pseudo est affecté à la variable de session.
	 *
	 * @see Action::run()
	 */
	public function run() {
		if ($this->database->checkPassword($_POST['nickname'], $_POST['password']) != true) {
			$this->setView(getViewByName("Default"));
			$this->setMessageView("Pseudo ou mot de passe incorrect.");
		}
		else {
			$this->setSessionLogin($_POST['nickname']);
			$this->setView(getViewByName("Default"));
			$this->setSessionId($this->database->getIdFromUser($_SESSION['login']));
			header("location:". $_SERVER['PHP_SELF']);
		}
	}

}

?>
