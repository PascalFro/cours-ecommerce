<?php

namespace App\Controller;

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

        $products = $productRepository->findBy([], [], 3);
        dd($products);

        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
        ]);
    }
}
