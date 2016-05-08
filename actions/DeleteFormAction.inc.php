<?php

require_once("actions/Action.inc.php");

class DeleteFormAction extends Action {

    /**
     * Delete un sondage
     * Revérifie que l'user est propriétaire du sondage
     *
     * @see Action::run()
     */
    public function run() {

        if ($this->getSessionLogin()===null) {
            $this->setMessageView("Vous devez être authentifié.", "alert-error");
            return;
        }


        $rep = sondageDelete($_GET["sid"]);
        if ($rep) {
            $this->setMessageView("Vous avez correctement effacé votre sondage.");
            return;
        }


        $this->setMessageView("Erreur.", "alert-error");



    }

}

?>
