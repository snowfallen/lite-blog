<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class RegistrationController extends AbstractController
{
    #[Route('/registration', name: 'registration')]
    public function registration(Request $request,ManagerRegistry $doctrine,UserPasswordHasherInterface $passwordHasher)
    {
        $form = $this->createFormBuilder()
            ->add('username')
            ->add('password',RepeatedType::class,[
                'type' => PasswordType::class,
                'required' => true,
                'first_options' => ['label'=>'Password'],
                'second_options' => ['label'=>'Repeat Password'],
            ])
            ->add('registration',SubmitType::class,[
                'attr' => [
                    'class' => 'btn-success float-end m-2'
                ]
            ])
            ->getForm();

        $form->handleRequest($request);
        if($form->isSubmitted()){
            $data = $form->getData();
            $user = new User();
            $user->setUsername($data['username']);
            $user->setPassword($passwordHasher->hashPassword($user,$data['password']));

            $em = $doctrine->getManager();
            $em->persist($user);
            $em->flush();
            return $this->redirect($this->generateUrl('app_login'));
        }
        return $this->render('registration/index.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
