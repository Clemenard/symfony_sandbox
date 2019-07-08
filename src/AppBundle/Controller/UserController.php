<?php
namespace AppBundle\Controller;

use AppBundle\Entity\User;
use AppBundle\Entity\UserExtern;
use AppBundle\Repôsitory\UserRepository;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;



class UserController extends Controller
{
/**
 * @Route("/create", name="create")
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

/**
 * @Route("/logout", name="logout")
 */
public function logoutAction()
{
    unset($_SESSION['profil']);
    return $this->redirectToRoute('accueil');
}

/**
 * @Route("/form", name="form")
 */
public function formAction()
{
    unset($_SESSION['profil']);
    return $this->redirectToRoute('accueil');
}

/**
 * @Route("/login", name="login")
 */
public function loginAction()
{

$repo = $this -> getDoctrine() -> getRepository(User::class);
$_SESSION['profil'] = $repo -> findByEmail($_POST['email'])[0];
if(!$_SESSION['profil']){
    unset($_SESSION['users']);
      $url = 'https://reqres.in/api/users?page=1&per_page=6';
    $json = file_get_contents($url);
    $arrayJson = json_decode($json, true);
    foreach($arrayJson['data'] as $guestUser){

      $guestUser=new UserExtern($guestUser['first_name'],$guestUser['last_name'],$guestUser['email'],$guestUser['avatar']);
      if($guestUser->getEmail() == $_POST['email']){$_SESSION['profil']=$guestUser;}
      $_SESSION['users'][]=$guestUser;
      }}
    return $this->redirectToRoute('accueil');
}

// if you have multiple entity managers, use the registry to fetch them
public function editAction()
{
    $doctrine = $this->getDoctrine();
    $entityManager = $doctrine->getManager();
    $otherEntityManager = $doctrine->getManager('other_connection');
}

/**
* @Route("/", name="accueil")
*
* Affiche tous les produits
*/
public function accueilAction(){

  // Récupérer le repository (ProduitRepository) pour pouvoir manipuler la table produit
  $repo = $this -> getDoctrine() -> getRepository(User::class);

  // On récupere tous les produits
  $users = $repo -> findAll();


  // set and get session attributes
if(!isset($_SESSION['users'])){
  unset($_SESSION['users']);
    $url = 'https://reqres.in/api/users?page=1&per_page=6';
  $json = file_get_contents($url);
  $arrayJson = json_decode($json, true);
  foreach($arrayJson['data'] as $guestUser){

    $guestUser=new UserExtern($guestUser['first_name'],$guestUser['last_name'],$guestUser['email'],$guestUser['avatar']);
    $_SESSION['users'][]=$guestUser;

    }


}


  $params = array(
    'users' => $users,
    'exUsers' =>$_SESSION['users'],
    'profil' =>''
  );
  if(isset($_SESSION['profil'])){
$params['profil']=$_SESSION['profil'];}
  return $this -> render('User/index.html.twig', $params);
}
}
?>
