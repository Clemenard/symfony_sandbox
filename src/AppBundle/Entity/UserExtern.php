<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * UserExtern
 * @ORM\Table(name="user")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\UserRepository")
 */

class UserExtern extends User{


   public function __construct($firstName,$lastName,$email,$avatar) {

   $this->setEmail($email);
   $this->setFirstName($firstName);
   $this->setLastName($lastName);
   $this->setAvatar($avatar);

   return $this;
 }

}
?>
