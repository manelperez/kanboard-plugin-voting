<?php
namespace Kanboard\Plugin\Voting;

use Kanboard\Core\Plugin\Base;
use Kanboard\Plugin\Voting\Controller\VotingController;

class Plugin extends Base
{

    public function initialize()
    {
        $this->template->hook->attach('template:dashboard:page-header:menu', 'Voting:dashboard/menu');
        
        $this->hook->on('model:task:creation:prepare', array(
            $this,
            'TaskCreation'
        ));
        $this->hook->on('model:task:modification:prepare', array(
            $this,
            'TaskModification'
        ));
    }

    public function getClasses()
    {
        return array(
            'Plugin\Voting\Model' => array(
                'VotingModel',
                'ActivitiesEvaluationModel',
                'UserWeightModel'
            )
        );
    }

    public function TaskCreation(array &$values)
    {
        $voting = new VotingController($this->container);
        $voting->addVoting('Task has been created: \'' . $values['title'] . '\'');
    }

    public function TaskModification(array &$values)
    {
        $voting = new VotingController($this->container);
        $voting->addVoting('Task has been modified: \'' . $values['title'] . '\'');
    }

    public function getPluginName()
    {
        return 'Weighted voting';
    }

    public function getPluginAuthor()
    {
        return 'Manel Pérez';
    }

    public function getPluginVersion()
    {
        return '1.0.0';
    }

    public function getPluginDescription()
    {
        return 'Qualifying voting for a user task activity.';
    }

    public function getPluginHomepage()
    {
        return 'https://github.com/manelperez/plugin-qualifying-voting';
    }
}
