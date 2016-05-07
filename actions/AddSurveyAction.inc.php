<?php

require_once("model/Survey.inc.php");
require_once("model/Response.inc.php");
require_once("actions/Action.inc.php");

class AddSurveyAction extends Action {

	/**
	 * Traite les données envoyées par le formulaire d'ajout de sondage.
	 *
	 * Si l'utilisateur n'est pas connecté, un message lui demandant de se connecter est affiché.
	 *
	 * Sinon, la fonction ajoute le sondage à la base de données. Elle transforme
	 * les réponses et la question à l'aide de la fonction PHP 'htmlentities' pour éviter
	 * que du code exécutable ne soit inséré dans la base de données et affiché par la suite.
	 *
	 * Un des messages suivants doivent être affichés à l'utilisateur :
	 * - "c";
	 * - "Il faut saisir au moins 2 réponses.";
	 * - "Merci, nous avons ajouté votre sondage.".
	 *
	 * Le visiteur est finalement envoyé vers le formulaire d'ajout de sondage en cas d'erreur
	 * ou vers une vue affichant le message "Merci, nous avons ajouté votre sondage.".
	 *
	 * @see Action::run()
	 */
	 public function run() {
		 $reponse = [];
		 $nb_reponse = 5;// Nb de réponse à la question donnée
		 for ($i = 1; $i <= $nb_reponse; $i++) // Creation de l'array reponse contenant toute les reponses à la question
		 if ($_POST["responseSurvey$i"]){
		 	array_push($reponse, $value);
		 }
		 if (!$_POST["questionSurvey"]) {
			 $this->setAddSurveyFormView("La question est obligatoire.");
		 } else if(count($reponse) < 2 || count($reponse) > 5) { //max 5 sinon on pouvais tricher pour en mettre plus
			 $this->setAddSurveyFormView("Il faut saisir au moins 2 réponses.");
		 } else {
			 $survey = new Survey($this->getSessionLogin(), $_POST["questionSurvey"]);
			 $survey->setResponses($reponse);
			 if ($this->database->saveSurvey($survey))
			 $this->setMessageView("Sondage ajouté");
			 else
			 ($this->setAddSurveyFormView("Il y a un probleme dans votre requete"));
		 }
	 }


	private function setAddSurveyFormView($message) {
		$this->setView(getViewByName("AddSurveyForm"));
		$this->getView()->setMessage($message, "alert-error");
	}

}

?>
