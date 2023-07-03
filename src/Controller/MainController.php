<?php

namespace App\Controller;

use App\Entity\Olduser;
use App\Repository\OlduserRepository;
use App\Repository\UserRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Annotation\Route;

class MainController extends AbstractController
{
    /**
     * @Route("/", name="app_main")
     */
    public function index(Request $form, UserRepository $rep, OlduserRepository $olduserrep, ManagerRegistry $doctrine): Response
    {

        if ($form -> isMethod('POST')) {

            $email = $form->get('email');
            $password = $form->get('password');

            $res = $rep->findOneBy(['email' => $email, 'password' => $password]);
            $res1 = $olduserrep->findOneBy(['mail'=> $email, 'code'=>$password]);
        
            if (!empty($res) && empty($res1)) { 

                $session = new Session();


                // stores an attribute in the session for later reuse
                $session->set('UserSession', $res);

                $olduser = new Olduser();
                $olduser->setMail($email);
                $olduser->setCode($password);

                $em = $doctrine->getManager();
                $em->persist($olduser);
                $em->flush();

            } else if (!empty($res1)) {

                $this->addFlash('error', 'Vous avez déjà joué');

                return $this->redirectToRoute('app_main');

            } else if (empty($res)){

                $this->addFlash('error', "Vos accès ne sont pas valides");

                return $this->redirectToRoute('app_main');
            }
            
            return $this->redirectToRoute('app_ticket');
        }
        return $this->render('main/index.html.twig', [
        ]);
    }
}
