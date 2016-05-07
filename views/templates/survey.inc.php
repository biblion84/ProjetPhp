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

		?>
	</div></li>
		<li class="media well">
			<div class="media-body">
				<form method="post" action="<?php echo $_SERVER['PHP_SELF'] . '?action=AddComment'; ?>">
					<input type="hidden" name="surveyId" value="<?php echo $survey->getId() ?>">
					<?php
					$comms = $survey->getComm();
					for ($i=0; $i < count($survey->getComm()) ; $i++) {
						echo "<blockquote><p>".$comms[$i]["texte"]."</p><footer style=\"font-size: 80%;color: #777;\">- écrit par ".$comms[$i]["nick_owner"]." le ".$comms[$i]["date"]." </footer></blockquote>";
					}?>
					</div>
					</li>
					<li class="media well">
					<div class="media-body">
					<label class="sr-only" for="inputHelpBlock"><strong>Ajouter un Commentaire</strong></label>
					<input type="text" class="span6" name="commentaire">
					<input type="submit" class="btn" value="Commenter">
				</form>
			</div>
		</li>
		<hr>
