<?php

namespace App\Controller;

use App\Entity\Lot;
use App\Entity\Result;
use App\Entity\Winner;
use App\Repository\CodeRepository;
use App\Repository\LotRepository;
use App\Repository\ResultRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionBagProxy;
use Symfony\Component\Routing\Annotation\Route;

class TicketController extends AbstractController
{
    /**
     * @Route("/ticket", name="app_ticket")
     */
    public function index(Request $request, ResultRepository $resultRepository): Response
    {

        function random($min, $max, $quantity)
        {
            $numbers = range($min, $max);
            shuffle($numbers);
            return array_slice($numbers, 0, $quantity);
        }


        $em = $this->getDoctrine()->getManager();
        $repo = $em->getRepository(Result::class);
        $quantity = 1; 
        $totalRowsTable = $repo->createQueryBuilder('a')->select('count(a.id)')->getQuery()->getSingleScalarResult();// This will be in this case 10 because i have 10 records on this table
        $random_ids = random(1, $totalRowsTable, $quantity);
        
        $random_code = $repo->createQueryBuilder('a')
            ->where('a.id IN (:ids)') // if is another field, change it
            ->setParameter('ids', $random_ids)
            ->setMaxResults($totalRowsTable)// Add this line if you want to give a limit to the records (if all the ids exists then you would like to give a limit)
            ->getQuery()
            ->getResult();

        $res = $resultRepository->findOneById($random_code);


        return $this->render('ticket/index.html.twig', [
            'result' => $res,
        ]);
    }

    /**
     * @Route("lot/{id}", name="app_lot")
     */
    public function lot(LotRepository $lotRepository, CodeRepository $codeRepository): Response
    {
        function random1($min, $max, $quantity)
        {
            $numbers = range($min, $max);
            shuffle($numbers);
            return array_slice($numbers, 0, $quantity);
        }


        $em = $this->getDoctrine()->getManager();
        $repo = $em->getRepository(Lot::class);
        $quantity = 1; 
        $totalRowsTable = $repo->createQueryBuilder('a')
                                ->select('count(a.id)')
                                ->where('a.stock != 0')                                
                                
                                ->getQuery()
                                ->getSingleScalarResult();// This will be in this case 10 because i have 10 records on this table
        $random_ids = random1(5, $totalRowsTable, $quantity);
        
        $random_code = $repo->createQueryBuilder('a')
            ->where('a.stock != 0')
            ->andWhere('a.id IN (:ids)') // if is another field, change it
            ->setParameter('ids', $random_ids)
            ->setMaxResults($totalRowsTable)// Add this line if you want to give a limit to the records (if all the ids exists then you would like to give a limit)
            ->getQuery()
            ->getResult();

            $lots = $lotRepository->findOneById($random_code);
            $code = $codeRepository->findOneByLot($lots);  

                   
        return $this->render('ticket/lot.html.twig', [
            'lot' => $lots,
            'code' => $code
        ]);
    }

    /**
     * @Route("/tonlot/{id}/{id2}", name="tonlot")
     */
    public function recup($id, $id2,LotRepository $lotRepository, CodeRepository $codeRepository, ManagerRegistry $doctrine, Request $form): Response
    {
        $lot = $lotRepository->findOneById($id);
        $lotStock = $lot->getStock();

        $code = $codeRepository->findOneById($id2);
        $codeStock = $code->getStock();

        if ($lotStock >= 1) {
            
            $newStock = $lotStock - 1;
            $lot->setStock($newStock);
            $em = $doctrine->getManager();
            $em->persist($lot);
            $em->flush();

        }
        if ($codeStock >= 1) {
            
            $newStock = $codeStock - 1;
            $code->setStock($newStock);
            $em = $doctrine->getManager();
            $em->persist($code);
            $em->flush();

        } else {
            $em->remove($code);
        }

        if ($form -> isMethod('POST')) {

            $email = $form->get('email');
            $code = $form->get('code');

            $winner = new Winner();
            $winner->setDate(new \DateTime('now'));
            $winner->setEmail($email);
            $winner->setNewCode($code);

            $em = $doctrine->getManager();
            $em->persist($winner);
            $em->flush();
             
            return $this->redirectToRoute('app_fin');
            }
        return $this->render('ticket/recup.html.twig', [
            'code' => $code
        ]);
    }

    /**
     * @Route("fin", name="app_fin")
     */
    public function final(): Response
    {
        return $this->render('ticket/fin.html.twig', []);
    }
}
