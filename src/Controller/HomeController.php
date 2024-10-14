<?php

namespace App\Controller;

use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Controller\ProductRepository;


class HomeController extends AbstractController
{
    /**
     * @Route("/", name="homepage")
     */
    public function homepage(ProductRepository $productRepository): Response
    {
<<<<<<< HEAD

        $products = $productRepository->findBy([], [], 3);
        dd($products);

        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
=======
        $products = $productRepository->findBy([], [], 3);
        // dd($products);
        return $this->render('home/home.html.twig', [
            'products' => $products
>>>>>>> a35296f5f2d4861d8aba8c16cde95a0de0a233f3
        ]);
    }
}
