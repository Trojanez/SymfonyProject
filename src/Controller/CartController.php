<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Product;
use App\Entity\User;
use App\Entity\UserProduct;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class CartController extends AbstractController
{
    /**
     * @Route("/cart", options={"expose"=true}, name="cart")
     */
    public function index( Request $request )
    {
        $manager = $this->getDoctrine()->getManager();
        $session = $request->getSession();
        $header = $request->headers->get('x-user-id');

        // check if user subscribed or not
        $userSubscribed = $manager->getRepository(User::class)->getUserSubscribeInfo($header);

        $productsInCart = $session->get('product');

        if($productsInCart)
        {
            $productsIds = array_keys($productsInCart);
            $products = $manager->getRepository(Product::class)->showProductsFromCart($productsIds);
        } else {
            $products = null;
        }

        return $this->render('cart/cart.html.twig', array(
            'title' => 'Gameloft Games',
            'products' => $products,
            'users' => $userSubscribed
        ));
    }

    /**
     * @Route("/cart/clear", name="cartClear")
     */
    public function clear(Request $request)
    {
        $session = $request->getSession();

        if($session->get('product') != null)
        {
            $session->clear();
            return $this->redirectToRoute('home');
        } else {
            return new Response('Your cart is already empty');
        }
    }

    /**
     * @Route("/cart/download/{id}", name="cartDownload")
     */
    public function download(Request $request, $id)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $header = $request->headers->get('x-user-id');
        $session = $request->getSession();

        $productsInCart = $session->get('product');

        if($productsInCart)
        {
            $user = $entityManager->getRepository(User::class)->findOneBy(['phone' => $header]);
            $game = $entityManager->getRepository(Product::class)->find($id);
            $product = new UserProduct();

            $product->setUser($user);
            $product->setProduct($game);
            $product->setIsDownloaded(1);

            $entityManager->persist($product);
            $entityManager->flush();

            unset($productsInCart[$id]);
            $session->set('product', $productsInCart);

            return $this->redirectToRoute('cart');
        }
    }
}