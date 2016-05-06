<?php

require_once("actions/Action.inc.php");

class GetMySurveysAction extends Action {

	/**
	 * Construit la liste des sondages de l'utilisateur et le dirige vers la vue "SurveysView" 
	 * de façon à afficher les sondages.
	 *
	 * Si l'utilisateur n'est pas connecté, un message lui demandant de se connecter est affiché.
	 *
	 * @see Action::run()
	 */
	public function run() {
		$array_surveys = $this->database->loadSurveysByOwner($this->getSessionLogin());
		$this->setGetMySurveysView($array_surveys, '');
	}

	/**
	 * Récupère la vue GetMySurveysView.
	 *
	 * @param Surveys : le tableau contenant tous les objets Surveys
	 */

	private function setGetMySurveysView($surveys, $message) {
		$this->setView(getViewByName("Surveys"));
		$this->getView()->setSurveys($surveys);
		$this->getView()->setMessage($message);
	}

}

?>
