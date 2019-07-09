<?php
namespace AppBundle\Controller;

use AppBundle\Entity\User;
use AppBundle\Entity\UserExtern;
use AppBundle\Repository\UserRepository;
use AppBundle\Form\UserType;
use AppBundle\Form\ExternUserType;

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
public function formAction(Request $request)
{
    $form = $this->createForm(UserType::class);
      $form -> handleRequest($request);
    $params = array(
      'userForm' => $form -> createView(),
      'profil' =>'',
      'submitType' => 'Create'
    );

    if ($form->isSubmitted() && $form->isValid()) {
$entityManager = $this->getDoctrine()->getManager();
       $task = $form->getData();
        $entityManager->persist($task);
        $entityManager->flush();

       return $this->redirectToRoute('accueil');
   }


    if(isset($_SESSION['profil'])){
  $params['profil']=$_SESSION['profil'];}
    return $this -> render('User/form.html.twig', $params);
}

/**
 * @Route("/form/edit/{id}", name="edit")
 */
public function editAction(Request $request,$id)
{
  $repo = $this -> getDoctrine() -> getRepository(User::class);
    $form = $this->createForm(UserType::class,$repo -> find($id));
      $form -> handleRequest($request);
    $params = array(
      'userForm' => $form -> createView(),
      'profil' =>'',
      'submitType' => 'Edit'
    );

    if ($form->isSubmitted() && $form->isValid()) {
$entityManager = $this->getDoctrine()->getManager();
       $task = $form->getData();
        $entityManager->persist($task);
        $entityManager->flush();

       return $this->redirectToRoute('accueil');
   }


    if(isset($_SESSION['profil'])){
  $params['profil']=$_SESSION['profil'];}
    return $this -> render('User/form.html.twig', $params);
}

/**
 * @Route("/form/sync/{email}", name="sync")
 */
public function syncAction(Request $request,$email)
{
  unset($_SESSION['users']);
  $url = 'https://reqres.in/api/users?page=1&per_page=6';
$json = file_get_contents($url);
$arrayJson = json_decode($json, true);
foreach($arrayJson['data'] as $guestUser){

  $guestUser=new UserExtern($guestUser['first_name'],$guestUser['last_name'],$guestUser['email'],$guestUser['avatar']);
  if($guestUser->getEmail() == $email){$syncUser=$guestUser;}
  $_SESSION['users'][]=$guestUser;
  }
      $repo = $this -> getDoctrine() -> getRepository(User::class);
      $product = $repo -> findByEmail($email)[0];
$entityManager = $this->getDoctrine()->getManager();
       $product->setFirstName($syncUser->getFirstName());
       $product->setLastName($syncUser->getLastName());
       $product->setAvatar($syncUser->getAvatar());
        $entityManager->persist($product);
        $entityManager->flush();

    if(isset($_SESSION['profil'])){
  $params['profil']=$_SESSION['profil'];}
     return $this->redirectToRoute('accueil');
}

/**
 * @Route("/delete/{id}", name="delete")
 */
public function deleteAction($id)
{
  $entityManager = $this->getDoctrine()->getManager();
  $repo = $this -> getDoctrine() -> getRepository(User::class);
  $product = $repo -> find($id);
  $entityManager->remove($product);
$entityManager->flush();
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
