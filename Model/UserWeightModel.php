<?php
namespace Kanboard\Plugin\Voting\Model;

use Kanboard\Core\Base;

/**
 * User Weight Model
 *
 * @package Kanboard\Plugin\Voting\Model
 * @author Manel Pérez Clot <Open University of Catalonia (UOC)>
 * @version 1.0, 2018-05-13
 *         
 */
class UserWeightModel extends Base
{

    /**
     * nom de taula SQL
     * 
     * @var string
     */
    const TABLE = 'users';

    /**
     * modifica els registre d'usuari assignant el paràmetre del camp 'weight'.
     *
     * @access public
     * @param integer $user_id
     * @param integer $weight
     * @return boolean
     */
    public function updateWeight($user_id, $weight)
    {
        if ($user_id > 0) {
            //modifica
            $ret = $this->db->table(self::TABLE)
                ->eq('id', $user_id)
                ->update(['weight' => $weight]);
        }
        return $ret;
    }
}