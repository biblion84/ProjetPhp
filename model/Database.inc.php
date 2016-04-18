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
		$dbBd = "sondages";
		$dbPass = "root";
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
	 * - une table users(nickname char(20), password char(50));
	 * - une table surveys(id integer primary key autoincrement,
	 *						owner char(20), question char(255));
	 * - une table responses(id integer primary key autoincrement,
	 *		id_survey integer,
	 *		title char(255),
	 *		count integer);
	 */
	 private function CreateDatabase() {
	 	$this->connection->exec("CREATE TABLE IF NOT EXISTS users (".
	 	"id INT NOT NULL AUTO_INCREMENT,".
	 	"nickname VARCHAR(20) NOT NULL,".
	 	"email VARCHAR(255) NOT NULL,".
	 	"password VARCHAR(255) NOT NULL,".
	 	"PRIMARY KEY(id)".
	 	");");

	 	$this->connection->exec("CREATE TABLE IF NOT EXISTS surveys (".
	 	"id INT NOT NULL AUTO_INCREMENT,".
	 	"owner_id INT NOT NULL,". // Jointure avec 'users'
	 	"question VARCHAR(255) NOT NULL,".
	 	"choices TEXT NOT NULL,". // Stockées sous la forme 'reponse1;reponse2;reponse3'
	 	"responses TEXT NOT NULL,".  // Stockées sous la forme '45;25;68'
	 	"PRIMARY KEY(id),".
	  "FOREIGN KEY (owner_id) REFERENCES users(id)".
	 	");");

	 	$this->connection->exec("CREATE TABLE IF NOT EXISTS comments (".
	 	"id INT NOT NULL AUTO_INCREMENT,".
	 	"id_owner INT NOT NULL,". // Jointure avec 'users'
	 	"id_survey INT NOT NULL,". // Jointure avec 'surveys'
	 	"date DATE NOT NULL,".
	 	"texte TEXT NOT NULL,".
	 	"PRIMARY KEY(id),".
	  "FOREIGN KEY (id_owner) REFERENCES users(id),".
	  "FOREIGN KEY (id_survey) REFERENCES surveys(id)".
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
		$req->execute(array($nickname));
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
		$req->execute(array($nickname));
		$reponse = $req->rowCount();
		if($reponse == 0) return false; // = l'utilisateur n'existe pas

		$reponse = $req->fetch(PDO::FETCH_ASSOC);

		if($reponse['password'] == $password) return true;
		else return false;
		
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
			$req->execute(array("nickname" => $nickname,"password" => $password));
			return "true";
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
		$req->execute(array("nickname" => $nickname,"password" => $password));
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
		/* TODO START */
		/* TODO END */
		return true;
	}


	/**
	 * Sauvegarde une réponse dans la base de donnée et met à jour son indentifiant.
	 *
	 * @param Response $response Réponse à sauvegarder.
	 * @return boolean True si la sauvegarde a été réalisée avec succès, false sinon.
	 */
	private function saveResponse($response) {
		/* TODO START */
		/* TODO END */
		return true;
	}


	/**
	 * Charge l'ensemble des sondages créés par un utilisateur.
	 *
	 * @param string $owner Pseudonyme de l'utilisateur.
	 * @return array(Survey)|boolean Sondages trouvés par la fonction ou false si une erreur s'est produite.
	 */
	public function loadSurveysByOwner($owner) {
		/* TODO START */
		/* TODO END */
	}


	/**
	 * Charge l'ensemble des sondages dont la question contient un mot clé.
	 *
	 * @param string $keyword Mot clé à chercher.
	 * @return array(Survey)|boolean Sondages trouvés par la fonction ou false si une erreur s'est produite.
	 */
	public function loadSurveysByKeyword($keyword) {
		$req = $connection->prepare('SELECT * FROM surveys WHERE question=?');
		$req->execute(array('%'.$keyword.'%')); // pas sûr que ça marche, à tester
		$arraySurveys = $req->fetchAll();

		$arraySurveys = loadSurveys($arraySurveys);

		return $arraySurveys;
	}


	/**
	 * Enregistre le vote d'un utilisateur pour la réponse d'identifiant $id.
	 *
	 * @param int $id Identifiant de la réponse.
	 * @return boolean True si le vote a été enregistré, false sinon.
	 */
	public function vote($id) {
		/* TODO START */
		/* TODO END */
	}


	/**
	 * Construit un tableau de sondages à partir d'un tableau de ligne de la table 'surveys'.
	 * Ce tableau a été obtenu à l'aide de la méthode fetchAll() de PDO.
	 *
	 * @param array $arraySurveys Tableau de lignes.
	 * @return array(Survey)|boolean Le tableau de sondages ou false si une erreur s'est produite.
	 */
	private function loadSurveys($arraySurveys) {
		$surveys = array();

		for($i = 0 ; $i < count($arraySurveys) ; $i++) {
			$sondage = new Survey($arraySurveys[$i]['owner_id'], $arraySurveys[$i]['owner_id']);

			$sondage->setId($arraySurveys[$i]['id']);

			$choix = explode(';', $arraySurveys[$i]['choices']);
			for($i = 0 ; $i < count($choix) ; $i++) {
				$sondage->addResponse($choix[$i]);
			}

			$sondage->addVotes($arraySurveys[$i]['responses']);
			$sondage->computePercentages($votes);

			array_push($surveys, $sondage);
		}

		return $surveys;
	}



	/**
	 * Construit un tableau de réponses à partir d'un tableau de ligne de la table 'responses'.
	 * Ce tableau a été obtenu à l'aide de la méthode fetchAll() de PDO.
	 *
	 * @param Survey $survey Le sondage.
	 * @param array $arraySurveys Tableau de lignes.
	 * @return array(Response)|boolean Le tableau de réponses ou false si une erreur s'est produite.
	 */

	/* Ignorer cette fonction ? Elle peut être faite dans loadSurveys, et on n'a pas de table Responses donc elle devient inutile (Romain) */

	private function loadResponses($survey, $arrayResponses) {
		$responses = array();
		/* TODO START */
		/* TODO END */
		return $responses;
	}

}

?>
