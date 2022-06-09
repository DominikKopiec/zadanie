<?php

namespace App\Controller;

use App\Entity\Articles;
use App\Repository\ArticlesRepository;
use App\Entity\Units;
use App\Repository\UnitsRepository;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class ArticlesController extends AbstractController
{
    public function index(ArticlesRepository $ArticlesRepository, Request $request): Response
    {
        $article = new Articles();

        $form = $this->createFormBuilder($article)
            ->add('name', TextType::class, [
                'label' => 'Nazwa artykułu',
                'attr' => array('class' => 'form-control')])
            ->add('unit', EntityType::class, [
                'label' => 'Jednostka miary',
                'class' => Units::class,
                'choice_label' => 'name',
                'attr' => array('class' => 'form-control'),
            ])
            ->add('save', SubmitType::class, array(
                'label' => 'Dodaj',
                'attr' => array('class' => 'btn btn-primary mt-3')
            ))
            ->getForm();
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $task = $form->getData();
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($task);
                $entityManager->flush();
                return $this->redirectToRoute('articles');
            }

        
        $repo = $this->getDoctrine()->getRepository(Articles::class);
        $articles = $repo->findAll();

        return $this->render('articles/index.html.twig', array(
            'form' => $form->createView(),
            'articles' => $articles
        ));
    }
}
