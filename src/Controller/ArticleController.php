<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\Comment;
use App\Form\CommentType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;



/**
 * Class ArticleController
 * @package App\Controller
 * @Route("/article")
 */
class ArticleController extends AbstractController
{
    /**
     * @Route("/{id}")
     */
    public function index(Article $article, Request $request)
    {

        /*
         * Afficher toutes les info de l'article avec l'image s'il y en a une
         *
         * Sous l'article, si l'utilisateur n'est pas connecté, l'inviter à le faire pour pouvoir ecrire un commentaire
         * Sinon, lui afficher un formulaire avec un textarea pour pouvoir écrire un commentaire
         *
         * Nécessite une entité Comment :
         * - content (text en bdd)
         * - date de publication (datetime)
         * - user (l'utilisateur qui écrit le commentaire)
         * - un article (l'article sur lequel on écrit le commentaire)
         * Nécessite le formtype qui va avec contenant le textarea, le contenu du commentaire ne doit pas etre vide
         *
         * Lister les commentaires en dessous avec le nom utilisation, la date de publication et le contenu du message
         */

        $em = $this->getDoctrine()->getManager();

        $comment = new Comment();



        $form = $this->createForm(CommentType::class, $comment);

        $form->handleRequest($request);

        if($form->isSubmitted()){
            if($form->isValid()){

                $comment->setUser($this->getUser());

                $comment->setArticle($article);

                $em->persist($comment);
                $em->flush();

                $this->addFlash('success', 'Votre commentaire est bien enregistré');

                // redirection vers la page sur laquelle on est (avantage on est pas en http post)
                return $this->redirectToRoute(
                    // donne la route de la page courante
                    $request->get('_route'),
                    [
                        'id' => $article->getId()
                    ]
                );
            }
        }

        return $this->render(
            'article/index.html.twig',
            [
                'article' => $article,
                'form' => $form->createView(),
            ]
        );
    }


}
