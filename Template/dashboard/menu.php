<li>
 	<?= $this->modal->medium('star', t('Pending voting'), 'VotingController', 'viewPendingVotes', array('plugin' => 'Voting')) ?>
</li>
<li>
 	<?= $this->modal->medium('users', t('Users weight'), 'VotingController', 'viewUsersWeight', array('plugin' => 'Voting')) ?>
</li>