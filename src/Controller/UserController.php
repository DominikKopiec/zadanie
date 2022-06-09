<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Entity\Roles;
use App\Repository\RolesRepository;
use App\Entity\UserToDepot;
use App\Repository\UserToDepotRepository;
use App\Entity\Depots;
use App\Repository\DepotsRepository;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;

use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;



class UserController extends AbstractController
{
    public function index(): Response
    {
        $repository = $this->getDoctrine()->getRepository(User::class);
        $users = $repository->findAll();

        return $this->render('user/index.html.twig', array(
            'users'=> $users,
        ));
    }

    public function add(Request $request): Response {
        $repo = $this->getDoctrine()->getRepository(Roles::class);
        $roles = $repo->findAll();

        $user = new User();
        $form = $this->createFormBuilder($user)
            ->add('login', TextType::class, array('attr' => 
            array('class' => 'form-control')))
            ->add('password', TextType::class, array('attr' => 
            array('class' => 'form-control')))
            ->add('role', ChoiceType::class, [
                'choices' => $roles, 'choice_label' => 'name', 'attr' => array('class' => 'form-control')] )
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

                return $this->redirectToRoute('users');
            }

        return $this->render('user/newuser.html.twig', array(
            'form' => $form->createView()
        ));
    }

    public function showone(int $id, UserRepository $UserRepository): Response {
        $repository = $this->getDoctrine()->getRepository(User::class);
        $user = $repository->find($id);

        $repo = $this->getDoctrine()->getRepository(UserToDepot::class);
        $depots = $repo->findByUser($id);

        return $this->render('user/user.html.twig', array(
            'user' => $user,
            'depots' => $depots,
        ));
    }

    public function addDepot(int $id, Request $request, DepotsRepository $DepotsRepository): Response {
        $repo = $this->getDoctrine()->getRepository(Depots::class);
        $depots = $repo->findAll();

        $repository = $this->getDoctrine()->getRepository(User::class);
        $user = $repository->find($id);
       
        
        $usertodepot = new UserToDepot();
        $form = $this->createFormBuilder($usertodepot)
            ->add('user', ChoiceType::class, [
                'label' => 'Użytkownik',
                'choices' => [$user], 
                'choice_label' => 'id', 
                'attr' => array('class' => 'form-control', 'readonly' => true),

                ])
            ->add('depot', EntityType::class, [
                'label' => 'Magazyn',
                'class' => Depots::class,
                'choice_label' => 'name',
                'attr' => array('class' => 'form-control')  
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

                return $this->redirectToRoute('user', ['id' => $id]);
            }

        return $this->render('user/addDepot.html.twig', array(
            'form' => $form->createView(),
        ));
    }
}
