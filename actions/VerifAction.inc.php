<?php

require_once("actions/Action.inc.php");

class VerifAction extends Action {

	/**
	* Vérifie un email utilisateur
	*
	* @see Action::run()
	*/
	public function run() {
		$verif = $this->database->getVerifFromEmail($_GET["email"]);
		if ($verif == $_GET["verif"]) {
			$this->database->setVerifFromEmail($_GET["email"]);
			$this->setMessageView("Email ".$_GET["email"]." vérifié.");
		} else {
			$this->setMessageView("Erreur de vérification d'email.");
		}

	}

}
