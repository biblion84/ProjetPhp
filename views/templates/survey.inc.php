<br />
<li class="media well">
	<div class="media-body">
		<h4 class="media-heading"><?= $survey->getQuestion() ?></h4>
		<br>
	  <?php
		$tableauReponses = $survey->getResponses();
		$tableauPctages = $survey->getPercentages();

		for($i = 0 ; $i < count($survey->getResponses()) ; $i++) {
			?>
			<div class="fluid-row">
				<div class="span2"><?= $tableauReponses[$i]; ?></div>
				<div class="span2 progress progress-striped active">
					<div class="bar" style="width: <?= $tableauPctages[$i]; ?>%"></div>
				</div>
				<span class="span1">(<?= $tableauPctages[$i]; ?>%)</span>
				<form class="span1" method="post" action="<?php echo $_SERVER['PHP_SELF'].'?action=Vote';?>">
					<input type="hidden" name="responseId" value="<?php echo $i+1 ?>">
					<input type="hidden" name="surveyId" value="<?php echo $survey->getId() ?>">
					<input type="submit" style="margin-left:5px" class="span1 btn btn-small btn-danger" value="Voter">
				</form>
			</div>
		<?php
		}
		echo "</div></li><ul>";
		$comms = $survey->getComm();
		for ($i=0; $i < count($survey->getComm()) ; $i++) {
 			echo "<li>".$comms[$i]["texte"]."</li><p>".$comms[$i]["nick_owner"]."</p>";
		}
		echo "</ul>";
		?>
