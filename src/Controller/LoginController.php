<?php
namespace App\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use App\Entity\User;
use App\Repository\UserRepository;
/**
 * Class LoginController
 * @package App\Controller
 */
class LoginController extends AbstractController {
  public function login(Request $request, AuthenticationUtils $authenticationUtils) : Response {
    $errors = $authenticationUtils->getLastAuthenticationError();
    $lastUsername = $authenticationUtils->getLastUsername();


    return $this->render('User/login.html.twig', [
      'errors' => $errors,
      'username' => $lastUsername,
    ]);


  }
 
  public function logout() : Response {}
}