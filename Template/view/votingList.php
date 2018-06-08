<h2>
	<i class="fa fa-list-ul fa-fw" style="padding-right: 2em;"></i>
	<?= t('List of pending votes to perform') ?>
</h2>
<div class="panel">
	<h3><?= $user['username'] ?><br />
	<?= 'Your weight is ' . $user['weight'] . '%' ?></h3>
	<?php if (empty($voting)): ?>
    	<p class="alert"><?= t('You have no pending votes to perform.') ?></p>
	<?php else: ?>
		<table class="table-fixed table-stripped">
    	<?php foreach ($voting as $slot) : ?>
		<tr>
			<td rowspan="3" class="column-10" valign="top">
            	<?= $this->modal->replaceLink(t('Vote now'), 'VotingController', 'vote', array('plugin' => 'Voting', 'vote_id' => $slot[vote]['voting_id'])) ?>
            </td>
			<th class="column-20">
            	<?= t('Description') ?>
            </th>
			<td>
            	<?= t($slot[vote]['title']) ?>
            </td>
		</tr>
		<tr>
			<th class="column-20">	
            	<?= t('User') ?>
            </th>
			<td>
				<?= t($slot[owner]['username']) ?>
			</td>
		</tr>
		<tr>
			<th class="column-20">	
            	<?= t('Creation date') ?>
            </th>
			<td>
				<?= date('d/m/Y H:i:s', $slot[vote]['date_creation']) ?>
			</td>
		</tr>
		<tr>
			<td colspan="3"></td>
		</tr>
		<?php endforeach ?>
    	</table>
	<?php endif ?>
</div>
