<?php
require_once("views/View.inc.php");

class MessageView extends View {

	/**
	* Affiche un message à l'utilisateur.
	*
	* @see View::displayBody()
	*/
	public function displayBody() {
		echo '<div class="container"><br><br><br><br><div style="text-align:center" class="alert '.$this->style.'">'.$this->message.'</div></div>';
		if (isset($_SERVER['HTTP_REFERER'])) {
			echo "<a class=\"btn\" style=\"width: 20%; margin-left: 40%;\" href=\"".$_SERVER['HTTP_REFERER']."\">Retour</a>";
		}
	}

}
?>
