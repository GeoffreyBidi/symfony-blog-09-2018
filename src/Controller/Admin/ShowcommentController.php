<?php

namespace App\Controller\Admin;

use App\Entity\Article;
use App\Entity\Comment;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class ShowcommentController
 * @package App\Controller
 * @Route("/showcomment")
 */
class ShowcommentController extends AbstractController
{
    /**
     * @Route("/{id}")
     */
    public function index(Article $article)
    {

        $em = $this->getDoctrine()->getManager();

        $repository = $em->getRepository(Comment::class);

        $allComments = $repository->findBy(['article' => $article]);

        return $this->render(
            'admin/showcomment/index.html.twig',
            [
                'commentaires' => $allComments
            ]
        );
    }
}
