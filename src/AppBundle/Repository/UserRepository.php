<?php
use AppBundle\Entity\User;
use AppBundle\Entity\UserExtern;

namespace AppBundle\Repository;

/**
 * UserRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class UserRepository extends \Doctrine\ORM\EntityRepository
{

  public function findByMail($email){
    $query = $this->execRequest("SELECT * FROM users  WHERE email= :email ",array('email'=> $email));
    if( $query->rowCount() == 1 ){
      $query->setFetchMode(PDO::FETCH_CLASS,'User');
    $user=$query->fetch();
    return $user;
    }
    else {
        return false;
    }
      }


      function execRequest($req,$params=array()){
        $r = $this->db->prepare($req);
        if ( !empty($params) ){
          // sanatize et bindvalue
          foreach($params as $key => $value){
            $params[$key] = htmlspecialchars($value,ENT_QUOTES);
            $r->bindValue($key,$params[$key],PDO::PARAM_STR);
          }
        }
        $r->execute();
        if ( !empty( $r->errorInfo()[2] )){
          die('Request failed - please contact the administrator');
        }
        return $r;
      }
}
