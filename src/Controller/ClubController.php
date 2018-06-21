<?php

namespace App\Controller;

use App\Entity\Product;
use App\Entity\User;
use App\Entity\UserProduct;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class ClubController extends AbstractController
{
    /**
     * @Route("/club", name="club")
     */
    public function index(Request $request)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $header = $request->headers->get('x-user-id');

        $userSubscribed = $entityManager->getRepository(User::class)->getUserSubscribeInfo($header);

        if($userSubscribed)
        {
            $CurrentUserId = $entityManager->getRepository(User::class)->getUserId($header);
            if(!empty($CurrentUserId))
            {
                $product = $entityManager->getRepository(UserProduct::class)->getAllDownloadedGames($CurrentUserId);
            }

            $userDownloadDate = $entityManager->getRepository(User::class)->getSubscribedDateForUser($CurrentUserId);
            $userDownloads = $entityManager->getRepository(UserProduct::class)->getAmountOfDownloadedGames($CurrentUserId);
        } else {
            $userDownloadDate = null;
            $userDownloads = null;
            $product = null;
        }


        return $this->render('club/club.html.twig', array(
            'title' => 'Gameloft Games',
            'users' => $userSubscribed,
            'users_subscribed_date' => $userDownloadDate,
            'users_downloads' => $userDownloads,
            'products' => $product
        ));
    }

    /**
     * @Route("/unsubscribe")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */

    public function unsubscribe(Request $request)
    {
        $session = $request->getSession();
        $header = $request->headers->get('x-user-id');

        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository(User::class)->findOneBy(array('phone' => $header));

        $user->setIsSubscribe(0);
        $em->flush();
        $session->clear();

        return $this->redirectToRoute('home');
    }
}
