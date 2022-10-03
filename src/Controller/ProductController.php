<?php

namespace App\Controller;

use App\Entity\Product;
use App\Entity\Category;
use App\Form\ProductType;
use App\Repository\ProductRepository;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\Validator\Constraints\Collection;
use Symfony\Component\Validator\Constraints\GreaterThan;
use Symfony\Component\Validator\Constraints\LessThanOrEqual;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Validator\Constraints\Length;

class ProductController extends AbstractController
{
    /**
     * @Route("/{slug}", name="product_category")
     */
    public function category($slug, CategoryRepository $categoryRepository): Response
    {

        $category = $categoryRepository->findOneBy(
            [
                'slug' => $slug
            ]
        );

        if (!$category) {
            throw $this->createNotFoundException("La catégorie demandée n'existe pas !");
        }

        return $this->render('product/category.html.twig', [
            'slug' => $slug,
            'category' => $category
        ]);
    }

    /**
     * @Route("/{category_slug}/{slug}", name="product_show")
     *
     * @return void
     */
    public function show($slug, ProductRepository $productRepository)
    {

        $product = $productRepository->findOneBy([
            'slug' => $slug
        ]);

        if (!$product) {
            throw $this->createNotFoundException("Le produit demandé n'existe pas.");
        }

        return $this->render('product/show.html.twig', [
            'product' => $product
        ]);
    }

    /**
     * @Route("/admin/product/{id}/edit", name="product_edit")
     *
     * @return void
     */
    public function edit($id, ProductRepository $productRepository, Request $request, EntityManagerInterface $em, ValidatorInterface $validator)
    {
        // Validation de données Simples (scalaires) :
        // $age = 200;
        /* $resultat = $validator->validate($age, [
            new LessThanOrEqual([
                'value' => 90,
                'message' => "L'âge doit être inférieur à {{ compared_value }} mais vous avez donné {{ value }} !"
            ]),
            new GreaterThan([
                'value' => 0,
                'message' => "L'âge doit être supérieur à 0 !"
            ])
        ]); */

        // Validation de données complexes (tableaux) :
        /*         $client = [
            'nom' => 'FROMENTIN',
            'prenom' => 'Pa',
            'voiture' => [
                'marque' => 'Renault',
                'couleur' => ''
            ]
        ];

        $collection = new Collection([
            'nom' => new NotBlank(['message' => "Le nom ne doit pas être vide !"]),
            'prenom' => [
                new NotBlank(['message' => "Le prénom ne doit pas être vide !"]),
                new Length(['min' => 3, 'minMessage' => "Le prénom ne doit pas faire emoins de 3 caractères !"])
            ],
            'voiture' => new Collection([
                'marque' => new NotBlank(['message' => "La marque de la voiture est obligatoire !"]),
                'couleur' => new NotBlank(['message' => "La couleur de la voiture est obligatoire !"])
            ])
        ]);

        $resultat = $validator->validate($client, $collection); */

        // Validation grâce aux annotations :
        /* $product = new Product;
        $product->setName('Hello');

        $resultat = $validator->validate($product);

        if ($resultat->count() > 0) {
            dd("Il y a des erreurs ", $resultat);
        }

        dd("Tout va bien"); */

        $product = $productRepository->find($id);

        $form = $this->createForm(ProductType::class, $product);

        /*         $form->setData($product); */

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();

            /* Si on écrit tout à la main :
            Possibilité 1 :
            $response = new Response();

            $url = $urlGenerator->generate('product_show', [
                'category_slug' => $product->getCategory()->getSlug(),
                'slug' => $product->getSlug()
            ]);
            $response->headers->set('Location', $url);
            $response->setStatusCode(302);

            return $response;

            Possibilité 2 :
            $url = $urlGenerator->generate('product_show', [
                'category_slug' => $product->getCategory()->getSlug(),
                'slug' => $product->getSlug()
            ]);
            $response = new RedirectResponse($url);

            return $response;

            Possibilité 3 :
            $url = $urlGenerator->generate('product_show', [
                'category_slug' => $product->getCategory()->getSlug(),
                'slug' => $product->getSlug()
            ]);

            return $this->redirect($url);
            Et avec le moins de code possible :
        */
            return $this->redirectToRoute('product_show', [
                'category_slug' => $product->getCategory()->getSlug(),
                'slug' => $product->getSlug()
            ]);
        }

        $formView = $form->createView();

        return $this->render('product/edit.html.twig', [
            'product' => $product,
            'formView' => $formView
        ]);
    }

    /**
     * @Route("/admin/product/create", name = "product_create")
     *
     * @return void
     */
    public function create(FormFactoryInterface $factory, Request $request, SluggerInterface $slugger, EntityManagerInterface $em)
    {
        /* La factoryInterface permet d'aller dasn le détail sur certains élémetns du formulaire mais l'abstractController permet de créer un formulaire directement :
            $builder = $factory->createBuilder(ProductType::class);

            $form = $builder->getForm(); */

        $product = new Product;

        $form = $this->createForm(ProductType::class, $product);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $product->setSlug(strtolower($slugger->slug($product->getName())));

            /*             $product = new Product;
            $product->setName($data['name'])
                ->setShortDescription($data['shortDescription'])
                ->setPrice($data['price'])
                ->setCategory($data['category']); */
            $em->persist($product);
            $em->flush();

            return $this->redirectToRoute('product_show', [
                'category_slug' => $product->getCategory()->getSlug(),
                'slug' => $product->getSlug()
            ]);
        }


        $formView = $form->createView();

        return $this->render('product/create.html.twig', [
            'formView' => $formView
        ]);
    }
}
