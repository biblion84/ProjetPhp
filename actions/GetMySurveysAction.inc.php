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
		/* TODO START */
		$surveys = $this->database->loadSurveysByOwner($this->getSessionLogin());
		$this->setGetMySurveysView($surveys, '');
		/* TODO END */
	}


	private function setGetMySurveysView($surveys, $message) {
		$this->setView(getViewByName("Surveys"));
		$this->getView()->setMessage($message);
	}

}

?>
