<?php

require_once("actions/Action.inc.php");

class SignUpAction extends Action {

	/**
	* Traite les données envoyées par le formulaire d'inscription
	* ($_POST['signUpLogin'], $_POST['signUpPassword'], $_POST['signUpPassword2']).
	*
	* Le compte est crée à l'aide de la méthode 'addUser' de la classe Database.
	*
	* Si la fonction 'addUser' retourne une erreur ou si le mot de passe et sa confirmation
	* sont différents, on envoie l'utilisateur vers la vue 'SignUpForm' contenant
	* le message retourné par 'addUser' ou la chaîne "Le mot de passe et sa confirmation
	* sont différents.";
	*
	* Si l'inscription est validée, le visiteur est envoyé vers la vue 'MessageView' avec
	* un message confirmant son inscription.
	*
	* @see Action::run()
	*/
	public function run() {
		if (!isset($_POST['signUpLogin']) || !isset($_POST['signUpPassword']) || !isset($_POST['signUpPassword2']) || !isset($_POST['signUpEmail'])) {
			$this->setSignUpFormView("Erreur, il manque des informations");
		}
		elseif ($_POST['signUpPassword'] != $_POST['signUpPassword2']) {
			$this->setSignUpFormView("Les mots de passe ne correspondent pas");
		}
		elseif (!filter_var($_POST['signUpEmail'], FILTER_VALIDATE_EMAIL)) {
			$this->setSignUpFormView("L'email n'est pas correcte");
		}
		else {
			$verif = uniqid('GT4E_', true); //genere un identifiant unique
			$logonTest = $this->database->addUser($_POST['signUpLogin'], $_POST['signUpPassword'], $_POST['signUpEmail'], $verif);
			if ($logonTest != "true") {
				$message = $logonTest;
				$this->setSignUpFormView($message);
			}
			else {
				$this->database->sendEmailVerif($_POST['signUpEmail']);
				$message = "L'utilisateur ".$_POST['signUpLogin']." a été correctement créé. Merci de Valider l'adresse email.";
				$this->setMessageView($message);
			}

		}
	}

	private function setSignUpFormView($message) {
		$this->setView(getViewByName("SignUpForm"));
		$this->getView()->setMessage($message);
	}

}


?>
