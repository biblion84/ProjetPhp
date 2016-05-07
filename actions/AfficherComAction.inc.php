<?php

require_once("model/Survey.inc.php");
require_once("model/Response.inc.php");
require_once("actions/Action.inc.php");

class AfficherComAction extends Action {

    /**
     * Active ou désactive globalement les commentaires
     *
     * @see Action::run()
     */
    public function run() {
        if ($_SESSION["afficherCom"] == false) {
            $_SESSION["afficherCom"] = true;
            $this->setMessageView("Les Commentaire sont maintenant affichés.");
        }
        else {
            $_SESSION["afficherCom"] = false;
            $this->setMessageView("Les Commentaire sont maintenant cachés.");
        }
    }


}

?>
