# ProjetPhp
On va tout donner
A fond jte jure !

-----------------------------------------------------------------------------

Voilà l'array des actions possibles sur le site :

			'Default',
			'SignUpForm',
			'SignUp',
			'Logout',
			'Login',
			'UpdateUserForm',
			'UpdateUser',
			'AddSurveyForm',
			'AddSurvey',
			'GetMySurveys',
			'Search',
			'Vote');
			
Pour lancer une action il suffit de faire http://localhost/ProjetPhp/index.php?action=SignUpForm 

Dans la page principale, REQUEST récupère les POST ou GET ?action qu'on lui envoie et renvoie la portion de page qu'il faut en fonction.
Vous pouvez remplacer SignUpForm par l'action dont vous avez besoin.

-----------------------------------------------------------------------------

Voilà notre BDD actuelle :

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


