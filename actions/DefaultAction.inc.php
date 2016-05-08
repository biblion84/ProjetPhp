<?php

require_once("actions/Action.inc.php");

class DefaultAction extends Action {

	/**
	* Traite l'action par défaut.
	* Elle dirige l'utilisateur vers une page avec la liste de tous les sondages.
	*
	* @see Action::run()
	*/
	public function run() {
		$array_surveys = $this->database->loadAllSurveys();
		$this->setGetSurveysView($array_surveys, '');
	}

	/**
	* Récupère la vue GetSurveysView.
	*
	* @param Surveys : le tableau contenant tous les objets Surveys
	*/

	private function setGetSurveysView($surveys, $message) {
		$this->setView(getViewByName("Surveys"));
		$this->getView()->setSurveys($surveys);
		$this->getView()->setMessage($message);
	}
}
?>
