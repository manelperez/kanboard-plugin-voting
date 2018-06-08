<div class="panel">
	<h3>
		<i class="fa fa-lightbulb-o fa-fw" style="padding-right:2em;"></i>
		<?= 'Activities evaluation' ?>
		<br />
		<?= $voting['title'] ?>
	</h3>
	<form method="post" action="<?= $this->url->href('VotingController', 'evaluateActivity', array('plugin' => 'Voting')) ?>" autocomplete="off">
   		<?= $this->form->csrf() ?>
   		
   		<?= $this->form->hidden('voting_id', $values = array(voting_id => $voting['voting_id'])) ?>
    	    	 
    	<fieldset>
            <?= $this->form->label(t('Importance of action'), 'importance') ?>
            <?= $this->form->text('importance', $values) ?>
            <i class="form-help"><?= t('Vote between 0 and 10.') ?></i>
    	</fieldset>
    	<fieldset>
            <?= $this->form->label(t('Information accuracy'), 'accuracy') ?>
            <?= $this->form->text('accuracy', $values) ?>
            <i class="form-help"><?= t('Vote between 0 and 10.') ?></i>
    	</fieldset>
    	<fieldset>
            <?= $this->form->label(t('Right time'), 'time') ?>
            <?= $this->form->text('time', $values) ?>
            <i class="form-help"><?= t('Vote between 0 and 10.') ?></i>
    	</fieldset>
    	<fieldset>
            <?= $this->form->label(t('Initiative'), 'initiative') ?>
            <?= $this->form->text('initiative', $values) ?>
            <i class="form-help"><?= t('Vote between 0 and 10.') ?></i>
    	</fieldset>
    	<fieldset>
            <?= $this->form->label(t('Collaboration'), 'collaboration') ?>
            <?= $this->form->text('collaboration', $values) ?>
            <i class="form-help"><?= t('Vote between 0 and 10.') ?></i>
    	</fieldset>
    	
    	<?= $this->hook->render('template:config:application', array('values' => $values, 'errors' => $errors)) ?>
    	
    	<div class="form-actions">
    		<button type="submit" class="btn btn-blue"><?= t('Save') ?></button>
    	</div>
	</form>
</div>