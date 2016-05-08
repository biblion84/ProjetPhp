<?php
require_once("model/Survey.inc.php");
require_once("model/Response.inc.php");

class Database {

	private $connectDB;
	private $connection;

	/**
	 * Ouvre la base de données. Si la base n'existe pas elle
	 * est créée à l'aide de la méthode createDataBase().
	 */
	public function __construct() {
		$dbHost = "localhost";
		$dbBd = "sondage";
		$dbPass = "";
		$dbLogin = "root";
		$url = 'mysql:host='.$dbHost.';dbname='.$dbBd;
		//$url = 'sqlite:database.sqlite';

		// Les deux lignes de dessous ajoutés pour créer la BDD si elle n'existe pas
		$this->connectDB = new PDO("mysql:host=$dbHost", $dbLogin, $dbPass);
		$this->connectDB->exec ("CREATE DATABASE IF NOT EXISTS $dbBd;");

		$this->connection = new PDO($url, $dbLogin, $dbPass);
		if (!$this->connection) die("Impossible d'ouvrir la base de données");
		$this->createDataBase();
	}


	/**
	 * Initialise la base de données ouverte dans la variable $connection.
	 * Cette méthode crée, si elles n'existent pas, les trois tables :
	 * - une table users(pour les utilisateurs)
	 * - une table surveys(pour les sondages)
	 * - une table votes(pour les votes)
	 * - une table comments(Pour commenter un sondage)
	 */
	 private function CreateDatabase() {
	 	$this->connection->exec("CREATE TABLE IF NOT EXISTS users (".
	 	"id INT NOT NULL UNIQUE AUTO_INCREMENT,".
	 	"nickname VARCHAR(20) NOT NULL UNIQUE,".
	 	"email VARCHAR(255) NOT NULL,". //TODO : rajouter l'email lors de l'inscription
	 	"password VARCHAR(255) NOT NULL,".
	 	"PRIMARY KEY(id)".
	 	");");

	 	$this->connection->exec("CREATE TABLE IF NOT EXISTS surveys (".
	 	"id INT NOT NULL UNIQUE AUTO_INCREMENT,".
	 	"owner_id INT NOT NULL,". // Jointure avec 'users'
	 	"question VARCHAR(255) NOT NULL,".
		"responses TEXT NOT NULL,".  // Stockées sous la forme 'choix1;choix2;choix3'
	 	"PRIMARY KEY(id),".
	  "FOREIGN KEY (owner_id) REFERENCES users(id) ON DELETE CASCADE". // Permet de supprimer un soindage si un user est supprimé
	 	");");

	 	$this->connection->exec("CREATE TABLE IF NOT EXISTS comments (".
	 	"id INT NOT NULL UNIQUE AUTO_INCREMENT,".
		"nick_owner VARCHAR(20) NOT NULL,". // jointure avec 'users'
	 	"id_survey INT NOT NULL,". // Jointure avec 'surveys'
	 	"date DATE NOT NULL,".
	 	"texte TEXT NOT NULL,".
	 	"PRIMARY KEY(id),".
		"FOREIGN KEY (nick_owner) REFERENCES users(nickname) ON UPDATE CASCADE,".
	  "FOREIGN KEY (id_survey) REFERENCES surveys(id) ON DELETE CASCADE".
	 	");");

		$this->connection->exec("CREATE TABLE IF NOT EXISTS votes (". // Pour savoir si telle IP a voté pour qui.
		"id INT NOT NULL UNIQUE AUTO_INCREMENT,".
		"id_survey INT NOT NULL,". // Jointure avec 'surveys'
		"ip_adress TEXT NOT NULL,".
		"response INT NOT NULL,". // réponse 1 ou 2 ou 3 ...
		"PRIMARY KEY(id),".
		"FOREIGN KEY (id_survey) REFERENCES surveys(id) ON DELETE CASCADE".
		");");
	}


	/**
	 * Vérifie si un pseudonyme est valide, c'est-à-dire,
	 * s'il contient entre 3 et 15 caractères et uniquement des lettres, chiffres, tirets/underscores.
	 *
	 * @param string $nickname Pseudonyme à vérifier.
	 * @return boolean True si le pseudonyme est valide, false sinon.
	 */
	private function checkNicknameValidity($nickname) {
		if (strlen($nickname) >= 3 && strlen($nickname) <= 15 && preg_match("#^[a-zA-Z0-9_-]+$#", $nickname)) return true;
		else return false;
	}


	/**
	 * Vérifie si un mot de passe est valide, c'est-à-dire,
	 * s'il contient entre 6 et 30 caractères.
	 *
	 * @param string $password Mot de passe à vérifier.
	 * @return boolean True si le mot de passe est valide, false sinon.
	 */
	private function checkPasswordValidity($password) {
		if (strlen($password) >= 6 && strlen($password) <= 30) return true;
		else return false;
	}


	/**
	 * Vérifie la disponibilité d'un pseudonyme.
	 *
	 * @param string $nickname Pseudonyme à vérifier.
	 * @return boolean True si le pseudonyme est disponible, false sinon.
	 */
	private function checkNicknameAvailability($nickname) {
		$req = $this->connection->prepare('SELECT nickname FROM users WHERE nickname=?');
		$req->execute(array(htmlspecialchars($nickname)));
		$reponse = $req->rowCount();
		if($reponse == 0) return true; // si le tableau est vide = le nom n'est pas pris
		else return false;
	}


	/**
	 * Vérifie qu'un couple (pseudonyme, mot de passe) est correct.
	 *
	 * @param string $nickname Pseudonyme.
	 * @param string $password Mot de passe.
	 * @return boolean True si le couple est correct, false sinon.
	 */
	public function checkPassword($nickname, $password) {
		$password = hash('sha256', $password);
		$req = $this->connection->prepare('SELECT password FROM users WHERE nickname=?');
		$req->execute(array(htmlspecialchars($nickname)));
		$reponse = $req->rowCount();
		if($reponse == 0) return false; // = l'utilisateur n'existe pas

		$reponse = $req->fetch(PDO::FETCH_ASSOC);

		if($reponse['password'] == $password) return true;
		else return false;

	}

	/**
	 * Récupère un Nickname depuis un ID d'user.
	 *
	 * @param string $user_id ID d'user.
	 * @return string retourne le nickname.
	 */
	public function getUserFromId($user_id) {
		$req = $this->connection->prepare('SELECT nickname FROM users WHERE id=?');
		$req->execute(array(htmlspecialchars($user_id)));
		$reponse = $req->fetch(PDO::FETCH_ASSOC);
		return $reponse;
	}

	/**
	 * Récupère un ID depuis un user.
	 *
	 * @param string $user , le nick d'user.
	 * @return string retourne le id.
	 */
	public function getIdFromUser($user) {
		$req = $this->connection->prepare('SELECT id FROM users WHERE nickname=?');
		$req->execute(array(htmlspecialchars($user)));
		$reponse = $req->fetch(PDO::FETCH_ASSOC);
		return $reponse;
	}

	/**
	 * Récupère un Nickname depuis un ID du sondage.
	 *
	 * @param string $surv_id ID du sondage.
	 * @return string retourne le nickname.
	 */
	public function getUserFromSurveyId($surv_id) {
		$req = $this->connection->prepare('SELECT owner_id FROM surveys WHERE id=?');
		$req->execute(array(htmlspecialchars($surv_id)));
		$reponse = $req->fetch(PDO::FETCH_ASSOC);
		$reponse = $this->getUserFromId($reponse['owner_id']);
		return $reponse;
	}


	/**
	 * Ajoute un nouveau compte utilisateur si le pseudonyme est valide et disponible et
	 * si le mot de passe est valide. La méthode peut retourner un des messages d'erreur
	 *
	 * @param string $nickname Pseudonyme.
	 * @param string $password Mot de passe.
	 * @return boolean|string True si le couple a été ajouté avec succès, un message d'erreur sinon.
	 */
	public function addUser($nickname, $password) {
		if ($this->checkNicknameValidity($nickname) == false) {
			return "Le pseudo doit contenir entre 3 et 15 lettres.";
		}
		elseif ($this->checkPasswordValidity($password) == false) {
			return "Le mot de passe doit contenir entre 6 et 30 caractères.";
		}
		elseif ($this->checkNicknameAvailability($nickname) == false) {
			return "Le pseudo existe déjà.";
		}
		else {
			$password = hash('sha256', $password);
			$req = $this->connection->prepare('INSERT INTO `users` (`id`, `nickname`, `password`) VALUES (NULL, :nickname, :password);');
			$req->execute(array("nickname" => htmlspecialchars($nickname),"password" => htmlspecialchars($password)));
			return "true";
		}

	}

	public function addComm($comm, $survId, $login) {
			$req = $this->connection->prepare('INSERT INTO `comments` (`nick_owner`, `id_survey`, `date`, `texte`) VALUES ( :nick_owner, :id_survey, :datenow, :texte );');
			$req->execute(array("nick_owner" => htmlspecialchars($login),"id_survey" => htmlspecialchars($survId), "datenow" => date('Y-m-d'), "texte" => htmlspecialchars($comm)));
			return $req;
	}

	public function sondageDelete($survId) {
			$survIdOld = $survId;
			$survId = $this->getUserFromSurveyId($survId);
			if ($survId['nickname'] != $_SESSION['login']) {
				return false;
			}
			else {
				$req = $this->connection->prepare('DELETE FROM `surveys` WHERE id=?');
				$req->execute(array(htmlspecialchars($survIdOld)));
				return $req;
			}

	}

	/**
	 * Change le mot de passe d'un utilisateur.
	 * La fonction vérifie si le mot de passe est valide. S'il ne l'est pas,
	 * la fonction retourne le texte 'Le mot de passe doit contenir entre 3 et 10 caractères.'.
	 * Sinon, le mot de passe est modifié en base de données et la fonction retourne true.
	 *
	 * @param string $nickname Pseudonyme de l'utilisateur.
	 * @param string $password Nouveau mot de passe.
	 * @return boolean|string True si le mot de passe a été modifié, un message d'erreur sinon.
	 */
	public function updateUser($nickname, $password) {
		if (!$this->checkPasswordValidity($password))
		return false;

		$password = hash('sha256', $password);
		$req = $this->connection->prepare('UPDATE `users` SET `password` = :password WHERE `nickname` = :nickname;');
		$req->execute(array("nickname" => htmlspecialchars($nickname),"password" => htmlspecialchars($password)));
		return true;
	}


	/**
	 * Sauvegarde un sondage dans la base de donnée et met à jour les indentifiants
	 * du sondage et des réponses.
	 *
	 * @param Survey $survey Sondage à sauvegarder.
	 * @return boolean True si la sauvegarde a été réalisée avec succès, false sinon.
	 */
	public function saveSurvey($survey) {
		$req = $this->connection->prepare('INSERT INTO `surveys` (`id`, `owner_id`, `question`, `responses`) VALUES (NULL, :owner_id, :question, :responses);');
        $req_owner_id = $this->connection->prepare('SELECT `id` FROM `users` WHERE `nickname` = :nickname;');
		$nickname = $survey->getOwner();
        $req_owner_id->execute(array("nickname" => htmlspecialchars($nickname)));
        $owner_id = $req_owner_id->fetchColumn();
		$question = $survey->getQuestion();
		$responses = implode(';', $survey->getResponses()); // pour les mettre a la suite separe par un ';'

		if ($req->execute(array("owner_id" => htmlspecialchars($owner_id), "question" => htmlspecialchars($question), "responses" => htmlspecialchars($responses))))
			return true;
		return false;

	}

	/**
	 * Charge l'ensemble des sondages créés par un utilisateur.
	 *
	 * @param string $owner Pseudonyme de l'utilisateur.
	 * @return array(Survey)|boolean Sondages trouvés par la fonction ou false si une erreur s'est produite.
	 */
	public function loadSurveysByOwner($owner) {
		$owner = $this->getIdFromUser($owner);
		$req = $this->connection->prepare('SELECT * FROM surveys WHERE owner_id=?');
		$req->execute(array(htmlspecialchars($owner['id'])));
		$arraySurveys = $req->fetchAll();
		$arraySurveys = $this->loadSurveys($arraySurveys);

		return $arraySurveys;
	}

	/**
	 * Charge l'ensemble des sondages d'un id dans une variable.
	 *
	 * @param string $sid ID du sondage.
	 * @return array : Sondage trouvé par la fonction.
	 */
	public function loadSurveysById($sid) {
		$req = $this->connection->prepare('SELECT * FROM surveys WHERE id=?');
		$req->execute(array(htmlspecialchars($sid)));
		$array = $req->fetchAll();
		return $array;
	}

	/**
	 * Charge l'ensemble des commantaires d'un sondage.
	 *
	 * @param string $survey id du sondage.
	 * @return array(Survey)|boolean Sondages trouvés par la fonction ou false si une erreur s'est produite.
	 */
	public function loadCommBySurvey($survey) {
		$req = $this->connection->prepare('SELECT * FROM comments WHERE id_survey = ?');
		$req->execute(array(htmlspecialchars($survey)));
		$arraySurveys = $req->fetchAll();
		return $arraySurveys;
	}

	/**
	 * Charge l'ensemble des sondages dont la question contient un mot clé.
	 *
	 * @param string $keyword Mot clé à chercher.
	 * @return array(Survey)|boolean Sondages trouvés par la fonction ou false si une erreur s'est produite.
	 */
	public function loadSurveysByKeyword($keyword) {
		$req = $this->connection->prepare('SELECT * FROM surveys WHERE question LIKE :keyword');
		$req->execute(array(':keyword' => '%'.htmlspecialchars($keyword).'%'));
		$arraySurveys = $req->fetchAll();

		$arraySurveys = $this->loadSurveys($arraySurveys);

		return $arraySurveys;
	}


	/**
	 * Charge TOUS les sondages, pour les afficher sur la page d'accueil.
	 * Tri du plus récent au plus ancien.
	 * @return array(Survey)|boolean Sondages trouvés par la fonction ou false si une erreur s'est produite.
	 */
	public function loadAllSurveys() {
		$req = $this->connection->prepare('SELECT * FROM surveys ORDER BY id DESC');
		$req->execute();
		$arraySurveys = $req->fetchAll();
		$arraySurveys = $this->loadSurveys($arraySurveys);

		return $arraySurveys;
	}


	/**
	 * Vérifie si une IP n'a pas déja voté et Enregistre le vote pour la réponse.
	 *
	 * @param int $id Identifiant de la réponse.
	 * @param int $surveyId Identifiant du sondage.
	 * @return boolean True si le vote a été enregistré, false sinon.
	 */
	public function vote($id, $surveyId) {

		$ip_adress = $_SERVER["REMOTE_ADDR"];

		$req = $this->connection->prepare('SELECT * FROM votes WHERE `ip_adress` = :ip_adress AND `id_survey` = :id_survey ;');
		$req->execute(array("ip_adress" => htmlspecialchars($ip_adress), "id_survey" => htmlspecialchars($surveyId)));
		$reponse = $req->rowCount();
		if($reponse != 0) return false; // si le tableau est vide = l'ip a deja voté n'est pas pris

		$req_vote = $this->connection->prepare('INSERT INTO `votes` (`id_survey`, `ip_adress`, `response`) VALUES (:id_survey, :ip_adress, :response);');
		if ($req_vote->execute(array("id_survey" => htmlspecialchars($surveyId), "ip_adress" => htmlspecialchars($ip_adress), "response" => htmlspecialchars($id))) || $reponse != 0)
			return true;
		return false;

	}


	/**
	 * Construit un tableau de sondages à partir d'un tableau de ligne de la table 'surveys'.
	 * Ce tableau a été obtenu à l'aide de la méthode fetchAll() de PDO.
	 *
	 * @param array $arraySurveys Tableau de lignes.
	 * @return array(Survey)|boolean Le tableau de sondages ou false si une erreur s'est produite.
	 */
	private function loadSurveys($arraySurveys) {
		$all_surveys = array();

		for($i = 0 ; $i < count($arraySurveys) ; $i++) {
			$votes = "";
			$sondage = new Survey($arraySurveys[$i]['owner_id'], $arraySurveys[$i]['question']);
			$sondage->setId($arraySurveys[$i]['id']);

			$commantaires = $this->loadCommBySurvey($arraySurveys[$i]['id']);
			$sondage->setComm($commantaires);

			$reponses = explode(';', $arraySurveys[$i]['responses']);
			for($j = 0 ; $j < count($reponses) ; $j++) {
				$sondage->addResponse($reponses[$j]);
			}
			for($j = 0 ; $j < count($reponses) ; $j++) { // Permet de récupérer les votes depuis la table vote
				$req = $this->connection->prepare('SELECT * FROM votes WHERE `id_survey` = :id_survey AND `response` = :response ;');
				$req->execute(array("id_survey" => $arraySurveys[$i]['id'] , "response" => $j+1 ));
				$resp = $req->rowCount();
				$votes[] = $resp;
				$sondage->addVote($votes[$j]);
			}
			$sondage->computePercentages($votes);

			array_push($all_surveys, $sondage);
		}

		return $all_surveys;
	}

}

?>
