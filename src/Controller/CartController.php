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
            $userId = $entityManager->getRepository(User::class)->findOneBy(['phone' => $header]);
            $game = $entityManager->getRepository(Product::class)->findOneBy(['image' => $id]);
            $gameId = $entityManager->getRepository(Product::class)->getIdAccordingToImageName($id);

            $product = new UserProduct();

            $product->setUser($userId);
            $product->setProduct($game);
            $product->setIsDownloaded(1);

            $entityManager->persist($product);
            $entityManager->flush();

            unset($productsInCart[$gameId]);
            $session->set('product', $productsInCart);

            /**
             * $basePath can be either exposed (typically inside web/)
             * or "internal"
             */
            $basePath = $webPath = $this->get('kernel')->getProjectDir() . '/public/images';
            $filePath = $basePath.'/'.$id.'.png';
            $content = file_get_contents($filePath);

            // check if file exists
            $fs = new FileSystem();
            if (!$fs->exists($filePath)) {
                throw $this->createNotFoundException();
            }

            $response = new Response();

            //set headers
            $response->headers->set('Content-Type', 'image/png');
            $response->headers->set('Content-Disposition', 'attachment;filename="'.$id.'.png');

            $response->setContent($content);

            return $response;
        }
    }
}