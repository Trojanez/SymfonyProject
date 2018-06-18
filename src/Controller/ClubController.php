<?php

namespace App\Controller;

use App\Entity\Product;
use App\Entity\User;
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
        $header = $request->headers->get('x-user-id');
        $entityManager = $this->getDoctrine()->getManager();
        $user = new User();

        $userDownloadDate = $entityManager->getRepository(User::class)->getSubscribedDateForUser($header);
        $userDownloads = $entityManager->getRepository(User::class)->getDownloadsForUser($header);


        return $this->render('club/club.html.twig', array(
            'title' => 'Gameloft Games',
            'users_subscribed_date' => $userDownloadDate,
            'users_downloads' => $userDownloads
        ));
    }

    /**
     * @Route("/unsubscribe")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */

    public function unsubscribe(Request $request)
    {

        $user = new User();

        $header = $request->headers->get('x-user-id');

        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository(User::class)->findOneBy(array('phone' => $header));

        $user->setIsSubscribe(0);
        $em->flush();

        return $this->redirectToRoute('home');
    }
}
