<?php
/**
 * Created by PhpStorm.
 * User: Etudiant
 * Date: 03/12/2018
 * Time: 17:01
 */

namespace App\Controller\Admin;


use App\Entity\Article;
use App\Form\ArticleType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Class ArticleController
 * @package App\Controller\Admin
 * @Route("/article")
 */
class ArticleController extends AbstractController
{
    /**
     * @Route("/")
     */
    public function index()
    {
        /*
         * Faire la page qui liste les articles dans un tableau html
         * avec nom de la catégorie, nom de l'auteur et date au format francais(tous les champs sauf le content)
         */

        $em = $this->getDoctrine()->getManager();

        $repository = $em->getRepository(Article::class);

        $articles = $repository->findAll();
        // OU : $articles = $repository->findBy([], ['publicationDate' => 'desc']);

        dump($articles);

        return $this->render(
            'admin/article/index.html.twig',
            [
                'articles' => $articles
            ]
        );
    }

    /*
     * Ajouter la méthode edit() qui fait le rendu du formulaire et son traitement
     * mettre un lien 'ajouter' dans la page de liste
     *
     * Validation  : tous les champs sont obligatoires
     *
     * En création :
     * - setter l'auteur avec l'utilisateur connecté ($this->getUser() depuis le controleur)
     * - mettre la date de publication à maintenant
     *
     * Adapter la route et le contenu de la méthode pour que la page fonctionne en modification et ajouter le bouton modifier dans page de liste la liste
     *
     * Enregistrer l'article en bdd si le formulaire est bien rempli puis rediriger vers la liste avec un message de confirmation
     */

    /**
     * @param Request $request
     * @Route("/edition/{id}", defaults={"id": null}, requirements={"id":"\d+"})
     */
    public function edit(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $originalImage = null;

        if(is_null($id)){ // création d'une catégorie
            $article = new Article();
        }else{ // modification d'une catégorie
            $article = $em->find(Article::class, $id);

            if(!is_null($article->getImage())){
                // nom du fichier venant de la bdd
                $originalImage = $article->getImage();
                //on set l'image avec un objet File pour le traitement par le formulaire
                $article->setImage(
                    new File($this->getParameter('upload_dir') . $originalImage)
                );
            }

            // 404 si l'id recu dans l'url n'est pas en bdd
            if(is_null($article)){
                throw new NotFoundHttpException();
            }
        }


        // Création d'un form lié à la catégorie
        $form = $this->createForm(ArticleType::class, $article);

        // le formulaire analyse la requete HTTP et traitre le formulaire s'il a été soumis
        $form->handleRequest($request);

        dump($this->getUser());

        dump($article->setAuthor($this->getUser()));

        $article->setAuthor($this->getUser());

        // methode pour setter la date du form a maintenant OU POSSIBILITE DE FAIRE UN CONSTRUCT (VOIR CLASS ARTICLE)
        // $article->setPublicationDate(new \DateTime());

        // si le formulaire a été envoyé
        if($form->isSubmitted()){

            // si les validations à partir des annotations dans l'entité Category sont OK
            if($form->isValid()){

                /** @var UploadedFile $image */
                $image = $article->getImage();

                // si il y a une image uploadé
                if(!is_null($image)){
                    //
                    $filename = uniqid(). '.' . $image->guessExtension();

                    // équivalent de move_uploaded_file() permet de bouger le fichier des fichiers temporaires
                    $image->move(
                        // répertoire de destination
                        // cf le parametre upload_dir dans config/services.yaml
                        $this->getParameter('upload_dir'),
                        // le nom du fichier a bouger
                        $filename
                    );

                    // on set l'attribut image de l'article avec le nom de l'image pour enregistrement en bdd
                    $article->setImage($filename);

                    // En modification, on supprime l'ancienne image s'il y en a deja une en bdd
                    if(!is_null($originalImage)){
                       unlink($this->getParameter('upload_dir') . $originalImage);
                    }
                } else{
                    // sans upload d'image, pour la modification, on set l'attribut
                    $article->setImage($originalImage);
                }


                // enregistrement de la catégorie en bdd
                $em->persist($article);
                $em->flush();

                // message de confirmation
                $this->addFlash('success', 'L\'article est bien enregistré');
                // redirection vers la liste apres enregistrement
                return $this->redirectToRoute('app_admin_article_index');
            } else{
                $this->addFlash('error', 'Le formulaire contient des erreurs');
            }
        }


        // rendre la vue
        return $this->render(
            'admin/article/edit.html.twig',
            [
                // passage du formulaire au template
                'form' => $form->createView(),
                'original_image' => $originalImage
            ]
        );
    }

    /**
     * @Route("/suppression/{id}")
     */
    public function delete(Article $article) // param converter
    {
        $em = $this->getDoctrine()->getManager();

        $em->remove($article);
        $em->flush();

        $this->addFlash(
            'success',
            'L\'article est supprimée'
        );

        return $this->redirectToRoute('app_admin_article_index');
    }

}