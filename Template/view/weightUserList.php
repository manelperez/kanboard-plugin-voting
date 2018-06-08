<h2>
	<i class="fa fa-list-ul fa-fw" style="padding-right: 2em;"></i>
	<?= t('Users weight List') ?>
</h2>
<div class="panel">
	<h3><?= $user['username'] ?><br />
	<?= 'User percentage' ?></h3>
	<?php if (empty($groupUser)): ?>
    	<p class="alert"><?= t('There aren\'t users to show.') ?></p>
	<?php else: ?>
		<table class="table-fixed table-stripped">
    	<?php foreach ($groupUser as $slot) : ?>
		<tr>
			<th class="column-20">
            	<?= t($slot[user]['username']) ?>
            </th>
			<td>
            	<?= t($slot[user]['weight']) . '%' ?>
            </td>
		</tr>
		<?php endforeach ?>
    	</table>
	<?php endif ?>
</div>
