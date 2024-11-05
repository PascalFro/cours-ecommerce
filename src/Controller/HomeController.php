<?php

namespace App\Controller;

use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;



class HomeController extends AbstractController
{
    /**
     * @Route("/", name="homepage")
     */
    public function homepage(ProductRepository $productRepository): Response
    {
        $products = $productRepository->findBy([], [], 3);
        // dd($products);
        return $this->render('home/home.html.twig', [
            'products' => $products
        ]);
    }
}
