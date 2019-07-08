<?php
namespace AppBundle\Controller;

use AppBundle\Entity\User;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;


class UserController extends Controller
{
/**
 * @Route("/create")
 */
public function createAction()
{
    // you can fetch the EntityManager via $this->getDoctrine()
    // or you can add an argument to your action: createAction(EntityManagerInterface $entityManager)
    $entityManager = $this->getDoctrine()->getManager();

    $product = new User();
    $product->setFirstName('Keyboard');
    $product->setLastName('Mouse');
    $product->setEmail('cm.13@hotmail.fr');
    $product->setAvatar('https://picsum.photos/id/239/200/300');

    // tells Doctrine you want to (eventually) save the Product (no queries yet)
    $entityManager->persist($product);

    // actually executes the queries (i.e. the INSERT query)
    $entityManager->flush();

    return new Response('Saved new user with id '.$product->getId());
}

// if you have multiple entity managers, use the registry to fetch them
public function editAction()
{
    $doctrine = $this->getDoctrine();
    $entityManager = $doctrine->getManager();
    $otherEntityManager = $doctrine->getManager('other_connection');
}

/**
* @Route("/user", name="accueil")
*
* Affiche tous les produits
*/
public function accueilAction(){

  // Récupérer le repository (ProduitRepository) pour pouvoir manipuler la table produit
  $repo = $this -> getDoctrine() -> getRepository(User::class);

  // On récupere tous les produits
  $users = $repo -> findAll();




  $params = array(
    'users' => $users
  );

  return $this -> render('User/index.html.twig', $params);
}
}
?>
