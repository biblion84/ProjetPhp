<?php
class Survey {

	private $id;
	private $owner;
	private $question;
	private $responses;
	private $votes; // le nombre de votes ('16;87;51;74' par ex.)
	private $pourcentages; // idem mais avec ('30.01;48.58...')

	public function __construct($owner, $question) {
		$this->id = null;
		$this->owner = $owner;
		$this->question = $question;
		$this->responses = array();
		$this->votes = array();
	}

	public function setId($id) {
		$this->id = $id;
	}

	public function getId() {
		return $this->id;
	}

	public function getOwner() {
		return $this->owner;
	}

	public function getQuestion() {
		return $this->question;
	}

	public function getResponses() {
		return $this->responses;
	}

	public function setResponses($responses) {
		$this->responses = $responses;
	}

	public function addResponse($response) {
		$this->responses[] = $response;
	}

	public function addVotes($votes) {
		$this->votes = explode(';', $votes);
	}

	public function computePercentages($votes) {
		$choix = explode(';', $votes);
		$total = 0;

		for($i = 0 ; $i < count($choix) ; $i++) {
			$total += $choix[$i];
		}

		for($i = 0 ; $i < count($choix) ; $i++) {
			$this->pourcentages[$i] = round(($choix[$i] / $total)*100);
		}
	}

}
?>
