<?php

namespace App\Controller;

use App\Entity\Product;
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

        $product = new Product();

        $manager = $this->getDoctrine()->getManager();

        $product = $manager->getRepository(Product::class)->getProductIfInCartAndNotDownloaded();

        return $this->render('cart/cart.html.twig', array(
            'title' => 'Gameloft Games',
            'products' => $product
        ));
    }

    /**
     * @Route("/cart/clear", name="cartClear")
     */
    public function clear( Request $request )
    {
        $session = $request->getSession();

        if($session->get('product') != null)
        {
            foreach($session->get('product') as $key => $value)
            {
                $product = new Product();

                $entityManager = $this->getDoctrine()->getManager();
                $product = $entityManager->getRepository(Product::class)->find($key);
                $product->setIsInCart(0);

                $entityManager->flush();

                $session->clear();
            }
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
        $session = $request->getSession();
        $product = new Product();

        $entityManager = $this->getDoctrine()->getManager();

        $product = $entityManager->getRepository(Product::class)->find($id);

        $product->setIsDownloaded(1);
        $product->setIsInCart(0);

        $entityManager->flush();

        $session->clear();

        return $this->redirectToRoute('cart');
    }
}