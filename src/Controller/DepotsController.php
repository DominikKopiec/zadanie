<?php

namespace App\Controller;

use App\Entity\Depots;
use App\Repository\DepotsRepository;
use App\Entity\Status;
use App\Repository\StatusRepository;
use App\Entity\Articles;
use App\Repository\ArticlesRepository;
use App\Entity\Units;
use App\Repository\UnitsRepository;
use App\Entity\UserToDepot;
use App\Repository\UserToDepotRepository;
use App\Entity\User;
use App\Repository\UserRepository;

use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;

use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class DepotsController extends AbstractController
{
    public function show(DepotsRepository $DepotsRepository): Response {


        $user = $this->getUser()->getRoles();
        $user_id = $this->getUser()->getId();

        if($user[0] == "ROLE_ADMIN") {
            $repository = $this->getDoctrine()->getRepository(Depots::class);
            $depots = $repository->findAll();
        }
        elseif($user[0] == "ROLE_USER") {
            $repository = $this->getDoctrine()->getRepository(UserToDepot::class);
            $depots = $repository->findByUser($user_id);
            
        }
        return $this->render('depots/index.html.twig', array(
            'depots'=> $depots,
        ));
    }
    public function showone(StatusRepository $StatusRepository, int $id): Response {
        $repository = $this->getDoctrine()->getRepository(Status::class);
        $status = $repository->findByDepot($id);
        return $this->render('depots/depot.html.twig', array(
            'status' => $status,
            'id' => $id
        ));
    }

    public function add(Request $request): Response {

        $depot = new Depots();

        $form = $this->createFormBuilder($depot)
            ->add('name', TextType::class, [
                'label' => 'Nazwa',
                'attr' => array('class' => 'form-control')])
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

                return $this->redirectToRoute('depots');
            }

        return $this->render('depots/newdepot.html.twig', array(
            'form' => $form->createView()
        ));
    

    }

    public function addArticle(int $id, Request $request): Response {

        $repository = $this->getDoctrine()->getRepository(Depots::class);
        $depot = $repository->find($id);

        $repo = $this->getDoctrine()->getRepository(Articles::class);
        $articles = $repo->findAll();
        
        $status = new Status();
        $form = $this->createFormBuilder($status)
            ->add('depot', ChoiceType::class, [
                'label' => 'Magazyn',
                'choices' => [$depot], 
                'choice_label' => 'id', 
                'attr' => array('class' => 'form-control', 'readonly' => true),
            ])
            ->add('article', EntityType::class, [
                'label' => 'Nazwa artykułu',
                'class' => Articles::class,
                'choice_label' => 'name',
                'attr' => array('class' => 'form-control'),
            ])
            ->add('code', TextType::class, [
                'label' => 'Kod artykułu',
                'attr' => array('class' => 'form-control')])
            ->add('value', TextType::class, [
                'label' => 'Ilość',
                'attr' => array('class' => 'form-control')])
            ->add('vat', TextType::class, array('attr' => 
            array('class' => 'form-control')))
            ->add('price', TextType::class, [
                'label' => 'Cena jednostkowa',
                'attr' => array('class' => 'form-control')])
            ->add('file', FileType::class, [
                'label' => 'Dodaj załącznik',
                'attr' => ['class' => 'form-control-file'],
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new File([
                        'mimeTypes' => [
                            'application/pdf',
                            'application/xml',
                        ],
                        'mimeTypesMessage' => 'Please upload a valid PDF document',
                    ])
                ],
            ])
            ->add('save', SubmitType::class, array(
                'label' => 'Dodaj',
                'attr' => array('class' => 'btn btn-primary mt-3')
            ))
            ->getForm();
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                if($request->files->get('form')['file']) {
                    $file = $request->files->get('form')['file'];
                    $uploads_directory = $this->getParameter('uploads_directory');
                    $filename =md5(uniqid()) .  '.'  . $file->guessExtension();
                    
                    $file->move(
                    $uploads_directory,
                    $filename
                    );
                }
            
                
                $task = $form->getData();

                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($task);
                $entityManager->flush();

                return $this->redirectToRoute('depot', ['id' => $id]);
            }

        return $this->render('depots/addArticle.html.twig', array(
            'form' => $form->createView(),
        ));
    }

    public function issueproduct(int $id, Request $request): Response {
        $status = new Status();

        $form = $this->createFormBuilder($status)
            ->add('code', TextType::class, [
                'label' => 'Kod artykułu',
                'attr' => array('class' => 'form-control')])
            ->add('value', TextType::class, [
                'label' => 'Ilość',
                'attr' => array('class' => 'form-control')])
            ->add('save', SubmitType::class, array(
                'label' => 'Wydaj',
                'attr' => array('class' => 'btn btn-primary mt-3')
            ))
            ->getForm();
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $code = $form->get('code')->getData();
                $difference = $form->get('value')->getData();
                
                $entityManager = $this->getDoctrine()->getManager();
                $statuss = $entityManager->getRepository(Status::class)->findByCode($code);
                if(($statuss[0]->getValue()-$difference)>0){
                    $statuss[0]->setValue($statuss[0]->getValue()-$difference);
                    $entityManager->flush();
                    return $this->redirectToRoute('depot', ['id' => $id]);
                }
                if(($statuss[0]->getValue()-$difference)==0){
                    $entityManager->remove($statuss[0]);
                    $entityManager->flush();
                    return $this->redirectToRoute('depot', ['id' => $id]);
                }

                
            }

        return $this->render('depots/issue.html.twig', array(
            'form' => $form->createView()
        ));
    

    }

    public function adduser(int $id, Request $request): Response {

        $repository = $this->getDoctrine()->getRepository(Depots::class);
        $depot = $repository->find($id);

        $usertodepot = new UserToDepot();
        $form = $this->createFormBuilder($usertodepot)
            ->add('depot', ChoiceType::class, [
                'label' => 'Magazyn',
                'choices' => [$depot], 
                'choice_label' => 'id', 
                'attr' => array('class' => 'form-control', 'readonly' => true),
            ])
            ->add('user', EntityType::class, [
                'label' => 'Użytkownik',
                'class' => User::class,
                'choice_label' => 'username',
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
                return $this->redirectToRoute('addusertodepot', ['id' => $id]);
            }


        $repo = $this->getDoctrine()->getRepository(UserToDepot::class);
        $users = $repo->findByDepot($id);
        
        return $this->render('depots/addUser.html.twig', array(
            'form' => $form->createView(),
            'users' => $users
            
        ));
    }
    
}
