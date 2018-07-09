<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class PageNotFoundController extends Controller
{
    /**
     * @Route("/{path}", name="error")
     */
    public function pageNotFoundAction()
    {
        return $this->render('error/error.html.twig');
    }
}