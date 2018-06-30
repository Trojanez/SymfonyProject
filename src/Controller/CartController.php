<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Product;
use App\Entity\User;
use App\Entity\UserProduct;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class CartController extends Controller
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
            // find products id according to parameters (keys) to show them in view
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
     * @Route("/cart/download/{image}", name="cartDownload")
     */
    public function download(Request $request, $image)
    {
        // create Manager, get session, get header
        $entityManager = $this->getDoctrine()->getManager();
        $header = $request->headers->get('x-user-id');
        $session = $request->getSession();

        $productsInCart = $session->get('product');

        // to download the game
        if($productsInCart)
        {
            // find user, game and game id according to image from the parameter
            $userId = $entityManager->getRepository(User::class)->findOneBy(['phone' => $header]);
            $game = $entityManager->getRepository(Product::class)->findOneBy(['image' => $image]);
            $allGameIds = $entityManager->getRepository(Product::class)->getIdAccordingToImageName($image);
            $gameId = array_column($allGameIds, 'id');
            $gameId = array_shift($gameId);

            $product = new UserProduct();

            $product->setUser($userId);
            $product->setProduct($game);
            $product->setIsDownloaded(1);

            $entityManager->persist($product);
            $entityManager->flush();

            // if downloaded, we need to delete the game from cart
            unset($productsInCart[$gameId]);
            $session->set('product', $productsInCart);

            // to download screen of the game
            $basePath = $webPath = $this->get('kernel')->getProjectDir() . '/public/images';
            $filePath = $basePath.'/'.$image.'.png';
            $content = file_get_contents($filePath);

            // check if file exists
            $fs = new FileSystem();
            if (!$fs->exists($filePath)) {
                throw $this->createNotFoundException();
            }

            $response = new Response();

            //set headers
            $response->headers->set('Content-Type', 'image/png');
            $response->headers->set('Content-Disposition', 'attachment;filename="'.$image.'.png');
            $response->setContent($content);

            return $response;
        } else
            {
            return new Response('Only subscribed users can download games');
            }
    }
}