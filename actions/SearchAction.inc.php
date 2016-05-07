<?php

require_once("actions/Action.inc.php");

class SearchAction extends Action {

	/**
	 * Construit la liste des sondages dont la question contient le mot clé
	 * contenu dans la variable $_POST["keyword"]. L'utilisateur est ensuite 
	 * dirigé vers la vue "ServeysView" permettant d'afficher les sondages.
	 *
	 * Si la variable $_POST["keyword"] est "vide", le message "Vous devez entrer un mot clé
	 * avant de lancer la recherche." est affiché à l'utilisateur.
	 *
	 * @see Action::run()
	 */
	public function run() {
		if($_POST["keyword"] !== '') {
			$array_surveys = $this->database->loadSurveysByKeyword($_POST["keyword"]);
			$this->setGetSurveysView($array_surveys, '');
		}
		else {
			$this->setMessageView("Veuillez entrer un terme à rechercher");
		}
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
