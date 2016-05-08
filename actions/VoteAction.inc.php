<?php

require_once("actions/Action.inc.php");

class VoteAction extends Action {

	/**
	* Récupère l'identifiant de la réponse choisie par l'utilisateur dans la variable
	* $_POST["responseId"] et met à jour le nombre de voix obtenues par cette réponse.
	* Pour ce faire, la méthode 'vote' de la classe 'Database' est utilisée.
	*
	* Erreur si l'ip a déja voté sur ce sondage
	*
	* Si une erreur se produit, un message est affiché à l'utilisateur lui indiquant
	* que son vote n'a pas pu être pris en compte.
	*
	* Sinon, un message de confirmation lui est affiché.
	*
	* @see Action::run()
	*/
	public function run() {

		$responseId = "";
		$surveyId = "";

		if (isset($_POST["responseId"])) $responseId = (int)trim($_POST["responseId"]);
		if (isset($_POST["surveyId"])) $surveyId = (int)trim($_POST["surveyId"]);

		$r = $this->database->vote((int)$responseId, (int)$surveyId);

		if ($r===false) {
			$this->setMessageView("Impossible d'enregistrer votre vote. Avez-vous déja voté ?", "alert-error");
			return;
		}

		$this->setMessageView("Votre vote a été enregistré.", "alert-success");
	}

}

?>
