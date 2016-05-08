<?php

require_once("actions/Action.inc.php");

class EditFormAction extends Action {

    /**
     * Traite les données envoyées par le formulaire de modification du sondage
     *
     * Si le sondage n'a pas ete affiche il l'affiche
     *
     * Sinon, la fonction appelle EditAction.inc.php . Elle transforme
     * les réponses et la question à l'aide de la fonction PHP 'htmlentities' pour éviter
     * que du code exécutable ne soit inséré dans la base de données et affiché par la suite.
     *
     * Un des messages suivants doivent être affichés à l'utilisateur :
     * - "La question est obligatoire.";
     * - "Il faut saisir au moins 2 réponses.";
     * - "Merci, nous avons modifié votre sondage.".
     *
     *
     * @see Action::run()
     */
    public function run() {

        if ($this->getSessionLogin()===null) {
            $this->setMessageView("Vous devez être authentifié.", "alert-error");
            return;
        }
        $reponse = [];
        $nb_reponse = 5;// Nb de réponse à la question donnée
        for ($i = 1; $i <= $nb_reponse; $i++) // Creation de l'array reponse contenant toute les reponses à la question
            if ($_POST["responseSurvey$i"]){
                $value = preg_replace("/[^a-zA-Z0-9 ]/", "", $_POST["responseSurvey$i"]); //remplace les carracteres spéciaux dans les réponses, ce qui fausserait les réponses (avec htmlspecialchar et les -)
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

        $this->setView(getViewByName("AddSurveyForm"));
    }

}

?>
