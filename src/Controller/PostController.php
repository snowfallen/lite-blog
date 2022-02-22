<?php

namespace App\Controller;

use App\Entity\Post;
use App\Form\PostType;
use App\Repository\PostRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class PostController extends AbstractController
{
    #[Route('/create', name: 'post')]
    public function post(Request $request , ManagerRegistry $doctrine)
    {
        $post = new Post();
        $form = $this->createForm(PostType::class , $post);
        $form->handleRequest($request);
        if ($form->isSubmitted()){
            $em = $doctrine->getManager();
            /** @var UploadedFile $file */
            $file = $request->files->get('post')['Attachment'];
            if ($file){
                $filename = md5(uniqid()) . '.' . $file->guessClientExtension();
                $file->move( // move our file
                    $this->getParameter('uploads_dir'),
                    $filename
                );
                $post->setImage($filename);
            }
            $em->persist($post);
            $em->flush();
            return $this->redirect($this->generateUrl('get'));
        }
        return $this->render('create/create.html.twig',[
            'form' => $form->createView()
        ]);
    }

    #[Route('/get', name: 'get')]
    public function get(PostRepository $postRepository)
    {
        $posts = $postRepository->findAll();

        return $this->render('main/index.html.twig',[
            'posts' => $posts
        ]);
    }

    #[Route('/show/{id}', name: 'show')]
    public function show(Post $post)
    {
        return $this->render('show/show.html.twig',[
            'post' => $post
        ]);
    }

    #[Route('/delete/{id}', name: 'delete')]
    public function delete(Post $post,ManagerRegistry $doctrine)
    {
        $em = $doctrine->getManager();
        $em->remove($post);
        $em->flush();

        return $this->redirect($this->generateUrl('get'));
    }
}
