<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\UserProduct;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class ClubController extends AbstractController
{
    /**
     * @Route("/club", name="club")
     */
    public function index(PaginatorInterface $paginator, Request $request)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $header = $request->headers->get('x-user-id');

        // if user subscribed or not
        $userSubscribed = $entityManager->getRepository(User::class)->getUserSubscribeInfo($header);

        if($userSubscribed) {
            $CurrentUserId = $entityManager->getRepository(User::class)->getUserId($header);
            if(!empty($CurrentUserId)) {
                //find all downloaded games according to user ID
                $product = $entityManager->getRepository(UserProduct::class)->getAllDownloadedGames($CurrentUserId);

                /**
                 * @var $paginator \Knp\Component\Pager\Paginator
                 */
                $result = $paginator->paginate(
                    $product,
                    $request->query->getInt('page', 1),
                    $request->query->getInt('limit', 3)
                );
            }

            $userDownloadDate = $entityManager->getRepository(User::class)->getSubscribedDateForUser($CurrentUserId);
            $userDownloads = $entityManager->getRepository(UserProduct::class)->getAmountOfDownloadedGames($CurrentUserId);
        } else {
            $userDownloadDate = null;
            $userDownloads = null;
            $product = null;
            $result = null;
        }


        return $this->render('club/club.html.twig', array(
            'title' => 'Gameloft Games',
            'users' => $userSubscribed,
            'users_subscribed_date' => $userDownloadDate,
            'users_downloads' => $userDownloads,
            'products' => $result
        ));
    }

    /**
     * @Route("/unsubscribe", name="unsubscribe")
     * @param Request $request
     * @return Response
     */

    public function unsubscribe(Request $request)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $session = $request->getSession();
        $header = $request->headers->get('x-user-id');

        $currentUserId = $entityManager->getRepository(User::class)->getUserId($header);
        $currentUserId = array_column($currentUserId, 'id');
        $currentUserId = array_shift($currentUserId);

        $user = $entityManager->getRepository(User::class)->findOneBy(['id' => $currentUserId]);

        if($user === null OR $user->getIsSubscribe() == false) {
            return new Response('Only subscribed users can be unsubscribed');
        } else {
            $user->setIsSubscribe(0);
            $entityManager->flush();
            $session->clear();

            return $this->redirectToRoute('home');
        }
    }
}
