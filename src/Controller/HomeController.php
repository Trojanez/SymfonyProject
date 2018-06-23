<?php
/**
 * Created by PhpStorm.
 * User: Max
 * Date: 6/7/2018
 * Time: 9:51 PM
 */

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Product;
use App\Entity\User;
use App\Entity\UserProduct;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class HomeController extends AbstractController
{
    /**
     * @Route("/", options={"expose"=true}, name="home")
     */
    public function index(Request $request)
    {
        // create Manager, get session, get header
        $entityManager = $this->getDoctrine()->getManager();
        $session = $request->getSession();
        $header = $request->headers->get('x-user-id');

        // get Current user ID according to phone number
        $CurrentUserId = $entityManager->getRepository(User::class)->getUserId($header);
        $CurrentUserId = array_column($CurrentUserId, 'id');
        $CurrentUserId = array_shift($CurrentUserId);

        //get user phone according to ID
        $userPhone = $entityManager->getRepository(User::class)->getUserPhone($CurrentUserId);
        $userPhone = array_column($userPhone, 'phone');
        $userPhone = array_shift($userPhone);

        if($header != $userPhone)
        {
            $session->clear();
        }

        $category1 = $entityManager->getRepository(Category::class)->find(1);
        $category2 = $entityManager->getRepository(Category::class)->find(2);

        // check if user exists in the DB
        $checkUser = $entityManager->getRepository(User::class)->findOneBy(['phone' => $header]);

        // check if user subscribed or not
        $userSubscribedAdditional = $entityManager->getRepository(User::class)->getUserSubscribeInfoAdditional($header);
        $userSubscribedAdditional = array_column($userSubscribedAdditional, 'is_subscribe');
        $userSubscribedAdditional = array_shift($userSubscribedAdditional);

        if($checkUser != null and $userSubscribedAdditional == "1")
        {
            $productsInCart = $session->get('product');
            if(!empty($CurrentUserId))
            {
                $downloadedGames = $entityManager->getRepository(UserProduct::class)->getDownloadedGames($CurrentUserId);
                $ids = implode(',', array_column($downloadedGames, "u_product_id"));
                $integerIDs = array_map('intval', explode(',', $ids));
            }

            if($productsInCart)
            {
                $productsIds = array_keys($productsInCart);

                $product1 = $entityManager->getRepository(Product::class)->showProductsNotInCart(1, $productsIds, $integerIDs);
                $product2 = $entityManager->getRepository(Product::class)->showProductsNotInCart(2, $productsIds, $integerIDs);
            } else
            {
                //find the first Category and its products
                $product1 = $entityManager->getRepository(Product::class)->findProductsByCategoryId(1, $integerIDs);
                $product2 = $entityManager->getRepository(Product::class)->findProductsByCategoryId(2, $integerIDs);
            }
        } else
        {
            $product1 = $entityManager->getRepository(Product::class)->findAllProductsByCategoryId(1);
            $product2 = $entityManager->getRepository(Product::class)->findAllProductsByCategoryId(2);
        }

        // subscribe user
        $user = new User();
        if ($request->isMethod('POST')) {

            $header = $request->headers->get('x-user-id');

            $user = $entityManager->getRepository(User::class)->findOneBy(array('phone' => $header));

            if($user)
            {
                $user->setIsSubscribe(1);
                $user->setDate();
                $entityManager->flush();

                $this->addFlash(
                    'success',
                    'You have successfully subscribed to game club! Download as much as you want.'
                );
                return $this->redirectToRoute('home');
            }else {
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
                return $this->redirectToRoute('home');
            }
        }

        // count amount of items added to the cart
        $count = 0;
        if($session->get('product') !== null)
        {
            $count = count($session->get('product'));
        }

        return $this->render('home/home.html.twig',[
            'title' => 'Gameloft Games',
            'product' => $product1,
            'category' => $category1,
            'product1' => $product2,
            'category1' => $category2,
            'users' => $userSubscribedAdditional,
            'header' => $header,
            'logged' => $user,
            'count' => $count
        ]);
    }

    /**
     * @Route("/category/save")
     */
    public function save()
    {
        $entityManager = $this->getDoctrine()->getManager();

        $category = new Category();
        $category->setName('Bestsellers');

        $product = new Product();
        $product->setName('Dungeon Hunter 5');
        $product->setDescription('Join the ranks of bounty hunters in the most exciting adventure in the style of hack-n-slash for mobile devices!');
        $product->setIsDownloaded(0);
        $product->setIsInCart(0);
        $product->setImage('dh5');

        $product->setCategory($category);

        $entityManager->persist($category);
        $entityManager->persist($product);

        $entityManager->flush();

        return new Response('Added new product with the name '.$product->getName() . '. This product is related to category ' . $category->getName());
    }

    /**
     * @Route("/products/save")
     */

    public function saveProduct()
    {
        $entityManager = $this->getDoctrine()->getManager();

        $product = new Product();
        $product->setName('Six-Guns');
        $product->setDescription('The first Android game to let you freely explore massive Wild West landscapes in Arizona and Oregon. 40 varied missions: Race horses, stop robbers, shoot targets, take back the fort and more!');
        $product->setImage('sg');

        $entityManager->persist($product);

        $entityManager->flush();

        return new Response('Added new product with the name '.$product->getName());
    }
}