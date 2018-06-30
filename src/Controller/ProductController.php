<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Product;
use App\Entity\Screen;
use App\Entity\User;
use App\Entity\UserProduct;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class ProductController extends AbstractController
{
    /**
     * @Route("/product/{id}", name="product")
     */
    public function index(Request $request, $id)
    {
        $header = $request->headers->get('x-user-id');

        $entityManager = $this->getDoctrine()->getManager();

        $product = $entityManager->getRepository(Product::class)->find($id);

        $screenSum = $entityManager->getRepository(Screen::class)->countScreenNumber($id);

        $screenArray = $entityManager->getRepository(Screen::class)->getScreenAccordingProduct($id);
        $screen = array_shift($screenArray);

        $userSubscribed = $entityManager->getRepository(User::class)->getUserSubscribeInfo($header);

        if ($request->isMethod('POST')) {

            $header = $request->headers->get('x-user-id');

            if($header === null)
            {
                return new Response('Unfortunately, we couldn\'t find a phone number =(');
            }

            $user = $entityManager->getRepository(User::class)->findOneBy(array('phone' => $header));

            if ($user)
            {
                $user->setIsSubscribe(1);
                $user->setDate();
                $entityManager->flush();

                $this->addFlash(
                    'success',
                    'You have successfully subscribed to game club! Download as much as you want.'
                );
                return $this->redirectToRoute('product', array('id' => $id));
            } else
                {
                $user = new User();
                $user->setPhone($header);
                $user->setIsSubscribe(1);
                $user->setDate();
                $user->setDownloads(0);

                $entityManager->persist($user);
                $entityManager->flush();

                $this->addFlash(
                    'success',
                    'You have successfully subscribed to game club! Download as much as you want.'
                );
                return $this->redirectToRoute('product', array('id' => $id));
            }
        }

        return $this->render('product/product.html.twig', array(
            'title' => 'Gameloft Games',
            'product' => $product,
            'users' => $userSubscribed,
            'screen' => $screen,
            'screens' => $screenArray,
            'screenSum' => $screenSum
        ));
    }

    /**
     * @Route("/product/add/{id}", name="productAdd")
     */
    public function set(Request $request, $id)
    {
        $id = intval($id);
        $session = $request->getSession();

        $productsInCart = [];

        if($session->get('product'))
        {
            $productsInCart = $session->get('product');
        }

        if(!array_key_exists($id, $productsInCart))
        {
            $productsInCart[$id] = 1;
        }

        $session->set('product', $productsInCart);

        return $this->redirectToRoute('home');
    }
}
