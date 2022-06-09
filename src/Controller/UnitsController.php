<?php

namespace App\Controller;

use App\Entity\Units;
use App\Repository\UnitsRepository;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

use Sensio\Bundle\FreamworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;


class UnitsController extends AbstractController
{
    public function index(UnitsRepository $UnitsRepository, Request $request): Response {
        $repository = $this->getDoctrine()->getRepository(Units::class);
        $units = $repository->findAll();

        $unit = new Units();

        $form = $this->createFormBuilder($unit)
            ->add('name', TextType::class, [
                'label' => 'Nazwa jednostki',
                'attr' => array('class' => 'form-control')])
            ->add('short', TextType::class, [
                'label' => 'Skrót',
                'attr' =>  array('class' => 'form-control')])
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

                return $this->redirectToRoute('units');
            }

        return $this->render('units/index.html.twig', array(
            'units'=> $units,
            'form' => $form->createView()
        ));
    

    }

}