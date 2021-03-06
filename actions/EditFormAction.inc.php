<?php

require_once("actions/Action.inc.php");

class EditFormAction extends Action {

    /**
    * Supprime le sondage et en recrée un avec les mêmes parramètres
    * Supprime aussi les votes, pour éviter tout abus !
    * Renvoie vers la vue de création de sondage avec les l'ancien sondage pré-remplis
    *
    * @see Action::run()
    */
    public function run() {

        if ($this->getSessionLogin()===null) {
            $this->setMessageView("Vous devez être authentifié.", "alert-error");
            return;
        }
        $survey = $this->database->loadSurveysById($_GET["sid"]);
        $rep = $this->database->sondageDelete($_GET["sid"]);
        if ($rep) {
            $_SESSION['survey'] = $survey;
            $this->setView(getViewByName("ModSurveyForm"));
            return;
        }
        else {
            $this->setMessageView("Erreur, vous ne pouvez pas modifier ce sondage.", "alert-error");
            return;
        }

    }

}

?>
