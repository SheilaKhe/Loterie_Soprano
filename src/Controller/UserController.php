<?php

namespace App\Controller;

use App\Entity\Olduser;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class UserController extends AbstractController
{
    /**
     * @Route("/login", name="app_login")
     */
    public function login(AuthenticationUtils $authenticationUtils, Request $request, ManagerRegistry $doctrine): Response
    {
        if ($this->getUser()) {

            
            return $this->redirectToRoute('app_ticket');
        }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();
        
        return $this->render('security/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error
        ]);
    }

    /**
     * @Route("/logout", name="app_logout")
     */
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }

    /**
     * @Route("log", name="app_log")
     */
    public function FunctionName(Request $request): Response
    {
        $session = new Session();
        $session->start();

        $mail = $request->request->get('email');
        $pwd = $request->request->get('password');

        // set and get session attributes
        $session->set('email', $mail);

        $session->get('name');

        // set flash messages
        $session->getFlashBag()->add('notice', 'Profile updated');

        
        return $this->render('$0.html.twig', []);
    }
}
