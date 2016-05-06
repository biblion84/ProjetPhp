<?php
class Survey {

	private $id;
	private $owner;
	private $question;
	private $responses;
	private $votes; // tableau contenant les votes, avec un vote par ligne
	private $pourcentages; // idem avec les pourcentages

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

	public function addVote($vote) {
		$this->votes[] = $vote;
	}

	public function computePercentages($votes) {
		$total = 0;

		for($i = 0 ; $i < count($votes) ; $i++) {
			$total += $votes[$i];
		}

		if($total != 0) { // pour empêcher les divisions par zéro
			for($i = 0 ; $i < count($votes) ; $i++) {
				$this->pourcentages[$i] = round(($votes[$i] / $total)*100);
			}
		}
		else
		{
			for($i = 0 ; $i < count($votes) ; $i++) {
				$this->pourcentages[$i] = 0;
			}

		}
	}

}
?>
