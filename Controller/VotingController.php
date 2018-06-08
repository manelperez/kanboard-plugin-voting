<?php
namespace Kanboard\Plugin\Voting\Controller;

use Kanboard\Controller\BaseController;

/**
 * Voting Controller
 *
 * @package Kanboard\Plugin\Voting\Controller
 * @author Manel Pérez Clot <Open University of Catalonia (UOC)>
 * @version 1.0, 2018-05-13
 *         
 */
class VotingController extends BaseController
{

    /**
     * Mostra la llista de votacions pendents de realitzar per a l'usuari actual
     *
     * @access public
     */
    public function viewPendingVotes()
    {
        // array que contindrà les votacions amb els seus usuaris a avaluar
        $voting = array();
        // actualitza els pesos de tots els usuaris
        $this->weightCalculation();
        // obté l'usuari actual
        $currentUser = $this->getUser();
        // obté les votacions pendents per a l'usuari
        $votes = $this->votingModel->getPendingVotes($currentUser['id']);
        
        // recorre cada votació pendent
        foreach ($votes as $vote) {
            // carrega a l'array la votació i l'usuari a avaluar
            $voting[$vote['id']] = array(
                'vote' => $vote,
                'owner' => $this->userModel->getById($vote['evaluated_user_id'])
            );
        }
        
        // mostra la vista amb la llista de votacions
        $this->response->html($this->helper->layout->app('Voting:view/votingList', array(
            'voting' => $voting,
            'user' => $currentUser
        )));
    }

    /**
     * Mostra la llista d'usuaris amb el seu corresponent pes percentual
     *
     * @access public
     */
    public function viewUsersWeight()
    {
		// actualitza els pesos de tots els usuaris
        $this->weightCalculation();
        // obté l'usuari actual
        $currentUser = $this->getUser();
        // obté els grups d'usuaris als que pertany l'usuari actual
        $groups = $this->groupMemberModel->getGroups($currentUser['id']);
        // obté tots els usuaris
        $allUsers = $this->userModel->getAll();
        
        // recorre tots els usuaris
        foreach ($allUsers as $user) {
            // recorre els grups de l'usuari actual per tenir en compte només els usuaris dels seus grups
            foreach ($groups as $group) {
                // obté un usuari que pertany a algun grup dels de l'usuari actual
                if ($this->groupMemberModel->isMember($group['id'], $user['id'])) {
                    // obté la qualitat de l'usuari, i en el cas que sigui 'null' o 0, li assigna un 1%
                    $user['weight'] = (empty($user['weight']) ? 1 : ($user['weight'] = 0 ? 1 : $user['weight']));
                    
                    // carrega a l'array la votació i l'usuari a avaluar
                    $groupUser[$user['id']] = array(
                        'user' => $user
                    );
                }
            }
        }
        
        // mostra la vista amb la llista d'usuaris i el seu pes
        $this->response->html($this->helper->layout->app('Voting:view/weightUserList', array(
            'groupUser' => $groupUser
        )));
    }

    /**
     * Crea una nova votació per a la acció que acaba de relitzar l'usuari
     *
     * @access public
     * @param string $title
     * @return boolean
     */
    public function addVoting($title)
    {
        // obté l'usuari actual
        $currentUser = $this->getUser(); // usuari que acaba de fer l'acció i serà avaluat
                                         
        // prepara les dades de la nova capçalera de la votació
        $values = array(
            'evaluated_user_id' => $currentUser['id'],
            'title' => $title,
            'date_creation' => time(),
            'date_completed' => null,
            'points' => null
        );
        // grava la nova capçalera de la votació
        $voting_id = $this->votingModel->addVoting($values);
        
        // obté els grups d'usuaris als que pertany l'usuari evaluat
        $groups = $this->groupMemberModel->getGroups($currentUser['id']);
        // obté tots els usuaris
        $users = $this->userModel->getAll();
        
        // comprova si la nova votació s'ha gravat correctament
        if ($voting_id) {
            // recorre tots els usuaris
            foreach ($users as $user) {
                // recorre els grups de l'usuari actual per assignar els usuaris dels seus grups
                foreach ($groups as $group) {
                    // obté un usuari que evaluarà en la votació
                    if ($this->groupMemberModel->isMember($group['id'], $user['id']) && $user['id'] != $currentUser['id']) {
                        // carrega l'usuari que evaluarà
                        $values = array(
                            'voting_id' => $voting_id,
                            'user_id' => $user['id']
                        );
                        
                        // afegeix l'usuari per a avaluar les activitats requerides en la votació
                        $evaluation_id = $this->activitiesEvaluationModel->evaluateActivity($values);
                        // salta a la comprovació del següent usuari
                        break;
                    }
                }
            }
            
            // creació amb èxit
            $this->flash->success(t('New voting has been created.'));
            return true;
        } else {
            // creació no efectuada
            $this->flash->failure(t('New voting could not be created.'));
            return false;
        }
    }

    /**
     * Mostra la votació sel·leccionada per avaluar les activitats de l'usuari
     *
     * @access public
     */
    public function vote()
    {
        $voting = array();
        // obté l'usuari actual
        $currentUser = $this->getUser();
        // obté el Id de votació sol·licitat
        $vote_id = $this->request->getIntegerParam('vote_id');
        // obté la llista de votacions pendents de realitzar per a l'usuari actual
        $votes = $this->votingModel->getPendingVotes($currentUser['id']);
        
        // recorre la llista de votacions
        foreach ($votes as $vote) {
            // si és la votació seleccionada
            if ($vote['voting_id'] == $vote_id) {
                // assigna la votació
                $voting = $vote;
                // surt de la iteració
                break;
            }
        }
        
        // mostra la vista amb les activitats a avaluar en la votació
        $this->response->html($this->helper->layout->app('Voting:view/evaluation', array(
            'voting' => $voting,
            'user' => $currentUser
        )));
    }

    /**
     * Crea una nova votació per a la acció que acaba de relitzar l'usuari.
     *
     * @access public
     */
    public function evaluateActivity()
    {
        // obté els valors sol·licitats de l'avaluació de cada activitat
        $values = $this->request->getValues();
        // obté l'usuari actual
        $currentUser = $this->getUser();
        
        // afegeix l'usuari i la data de votació als valors de la votació
        $values += array(
            'user_id' => $currentUser['id'],
            'date' => time()
        );
        
        // guarda els valors de la votació per a l'usuari actual
        if ($this->activitiesEvaluationModel->evaluateActivity($values)) {
            // gravació amb èxit
            $this->flash->success(t('Voting saved successfully.'));
        } else {
            // gravació no efectuada
            $this->flash->failure(t('Unable to save your voting.'));
        }
        
        // tanca la votació si és necessari
        $this->closeVoting($values['voting_id']);
        // torna a la llista de votacions pendents
        $this->viewPendingVotes();
    }

    /**
     * Tanca la votació en el cas que sigui necessari.
     * Aplica l'algoritme de càlcul de la qualitat de l'usuari evaluat
     * segons la ponderació de vots de la resta d'usuaris
     * en funció del seu pes (qualitat)
     *
     * @access private
     * @param integer $voting_id
     */
    private function closeVoting($voting_id)
    {
        $allUsersReady = true;
        // obté la votació amb el Id sol·licitat
        $voting = $this->votingModel->getVotingById($voting_id);
        
        // existeix la votació
        if (! empty($voting)) {
            
            // obté les avaluacions dels usuaris que pertanyen a la votació
            $evaluations = $this->activitiesEvaluationModel->getEvaluations($voting_id);
            
            // recorre les avaluacions dels usuaris que pertanyen a la votació
            foreach ($evaluations as $evaluation) {
                
                // comprova que l'avaluació s'hagi realitzat
                if (! empty($evaluation['date'])) {
                    // calcula la mitjana entre les activitats avaluades per un usuari
                    $userAvgMark = ($evaluation['importance'] + $evaluation['accuracy'] + $evaluation['time'] + $evaluation['initiative'] + $evaluation['collaboration']) / 5;
                    // obté l'usuari de la votació actual
                    $user = $this->userModel->getById($evaluation['user_id']);
                    
                    // afegeix a l'array la mitjana calculada i la qualitat de l'usuari al que pertany
                    $usersEval[] = array(
                        'mark' => $userAvgMark * $user['weight'],
                        'weight' => $user['weight']
                    );
                } else {
                    // marca que no tots els usuaris han finalitzat la votació
                    $allUsersReady = false;
                }
            }
            
            /*
             * comprova que tots els usuaris hagin votat
             * o hagin passat més de 2 dies de la data de creació de la votació
             */
            if ($allUsersReady == true || $voting['date_creation'] < strtotime("-2 day", time())) {
                // comprova que com a mínim hagin participat en la votació la meitat dels usuaris
                if ($usersEval >= (count($evaluations) / 2)) {
                    
                    // recorre cada usuari participant
                    foreach ($usersEval as $userEval) {
                        // suma de les puntuacions i els pesos dels usuaris
                        $partMark += $userEval['mark'];
                        $totalQty += $userEval['weight'];
                    }
                    
                    // obté el increment que s'aplicará sobre el pes actual de l'usuari, a partir de les votacions de la resta
                    $points = (($partMark / $totalQty) - 5) * 2;
                    // obté l'usuari avaluat en la votació
                    $evaluated_user = $this->userModel->getById($voting['evaluated_user_id']);
                    // actualitza el pes de l'usuari avaluat, incrementant o disminuint els punts obtinguts
                    $this->userWeightModel->updateWeight($evaluated_user['id'], $evaluated_user['weight'] + $points);
                    // informa a la votació de la puntuació obtinguda
                    $voting['points'] = $points;
                } else {
                    // la votació és nul·la perque no s'arriba al mínim de participants
                    $voting['points'] = null;
                }
                
                // data de tancament de la votació
                $voting['date_completed'] = time();
                // actualitza les dades de la votació
                $this->votingModel->addVoting($voting);
            }
        }
    }

    /**
     * Fa el repartiment de pesos per a tots els usuaris que treballen
     * amb l'usuari que te la sessió actual
     *
     * @access private
     */
    private function weightCalculation()
    {
        $usergrouping = array();
        // obté l'usuari actual
        $currentUser = $this->getUser();
        // obté els grups d'usuaris als que pertany l'usuari actual
        $groups = $this->groupMemberModel->getGroups($currentUser['id']);
        // obté tots els usuaris
        $allUsers = $this->userModel->getAll();
        
        // recorre tots els usuaris
        foreach ($allUsers as $user) {
            // recorre els grups de l'usuari actual per tenir en compte només els usuaris dels seus grups
            foreach ($groups as $group) {
                // obté un usuari que pertany a algun grup dels de l'usuari actual
                if ($this->groupMemberModel->isMember($group['id'], $user['id'])) {
                    // obté la qualitat de l'usuari, i en el cas que sigui 'null' o 0, li assigna un 1%
                    $user['weight'] = (empty($user['weight']) ? 1 : ($user['weight'] = 0 ? 1 : $user['weight']));
                    // afegeix a l'array d'agrupació d'usuaris a tenir en compte
                    $usergrouping[] = $user;
                    // suma els valors de les qualitats de tots els usuaris
                    $total += $user['weight'];
                    // salta al següent usuari
                    break;
                }
            }
        }
        
        // recorre l'agrupació d'usuaris a tenir en compte per controlar que ningú tingui majoria absoluta
        foreach ($usergrouping as $user) {
            // comprova si l'usuari té majoria absoluta
            if ($user['weight'] > ($total / 2)) {
                // en el cas de majoria absoluta, s'asigna directament un pes del 50%, ja que no es permet que sigui major
                $user['weight'] = ($total / 2);
                // recalculem el valor total (100% dels pesos)
                $total -= $user['weight'] - ($total / 2);
            }
        }
        
        // recorre l'agrupació d'usuaris per asignar el pes
        foreach ($usergrouping as $user) {
            // actualitza la qualitat de l'usuari amb el seu pes respecte de la resta (percentatge)
            $this->userWeightModel->updateWeight($user['id'], ($user['weight'] * 100) / $total);
        }
    }
}
