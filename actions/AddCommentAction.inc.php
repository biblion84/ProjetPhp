<?php

require_once("model/Survey.inc.php");
require_once("model/Response.inc.php");
require_once("actions/Action.inc.php");

class AddCommentAction extends Action {

    /**
     * Ajoute un commentaire appartenant à un sondahe
     *
     * la fonction ajoute le commentaire à la base de données. Elle transforme
     * les commentaires à l'aide de la fonction PHP 'htmlentities' pour éviter
     * que du code exécutable ne soit inséré dans la base de données et affiché par la suite.
     *
     * Le visiteur est finalement envoyé vers le formulaire d'ajout de commentaire en cas d'erreur
     * ou vers une vue affichant le message "Merci, nous avons ajouté votre commentaire.".
     * @see Action::run()
     */
    public function run() {
        if (!isset($_POST["surveyId"]) || !isset($_POST["commentaire"]) || !isset($_SESSION["login"]))
        {
            $this->setMessageView("Erreur, êtes-vous connecté ?");
            return false;
        }
        else {
            $req = $this->database->addComm($_POST["commentaire"], $_POST["surveyId"], $_SESSION["login"]);
            $this->setMessageView("Merci, nous avons ajoute votre commentaire.");
        }

    }


}

?>
