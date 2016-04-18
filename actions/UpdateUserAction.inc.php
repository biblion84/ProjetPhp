<?php

require_once("actions/Action.inc.php");

class UpdateUserAction extends Action {

	/**
	 * Met à jour le mot de passe de l'utilisateur en procédant de la façon suivante :
	 *
	 * Si toutes les données du formulaire de modification de profil ont été postées
	 * ($_POST['updatePassword'] et $_POST['updatePassword2']), on vérifie que
	 * le mot de passe et la confirmation sont identiques.
	 * S'ils le sont, on modifie le compte avec les méthodes de la classe 'Database'.
	 *
	 * Si une erreur se produit, le formulaire de modification de mot de passe
	 * est affiché à nouveau avec un message d'erreur.
	 *
	 * Si aucune erreur n'est détectée, le message 'Modification enregistrée.'
	 * est affiché à l'utilisateur.
	 *
	 * @see Action::run()
	 */
	public function run() {

		// Voir a l'interieur des setUpdateUserFormView pour voir l'utilite du if
		if (!$_POST['updatePassword2'] || !$_POST['updatePassword'] || !$_POST['lastpassword'])
		{
			$this->setUpdateUserFormView("Champ vide");
		} elseif (!$this->database->checkPassword($this->getSessionLogin(), $_POST['lastpassword'])) {
			$this->setUpdateUserFormView("Mauvais mot de passe");
		} elseif ($_POST['updatePassword'] !== $_POST['updatePassword2']) {
			$this->setUpdateUserFormView("Mots de passe non identique");
		} elseif ($this->database->updateUser($this->getSessionLogin(),$_POST['updatePassword'] )){
			$this->setMessageView("Changement effectue"); // Retour sur la view default + affichage message
		} else {
			$this->setUpdateUserFormView("Le mot de passe doit contenir entre 6 et 30 caractères");
		}
	}

	private function setUpdateUserFormView($message) {
		$this->setView(getViewByName("UpdateUserForm"));
		$this->getView()->setMessage($message, "alert-error");
	}

}

?>
