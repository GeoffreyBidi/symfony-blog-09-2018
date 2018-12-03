<?php
/**
 * Created by PhpStorm.
 * User: Etudiant
 * Date: 30/11/2018
 * Time: 14:54
 */

namespace App\Controller\Admin;


use App\Entity\Category;
use App\Form\CategoryType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class CategoryController
 * @package App\Controller\Admin
 * @Route("/categorie")
 */
class CategoryController extends AbstractController
{
    /**
     * 1° Page princile du BO qui affiche toutes nos catégories en base
     * @Route("/")
     */
    public function index()
    {
        $em = $this->getDoctrine()->getManager();

        $repository = $em->getRepository(Category::class);

        // $categories = $repository->findAll();
        // OU
        $categories = $repository->findBy([], ['name'=>'asc']); // ici meme chose qu'au dessus sauf qu'on lui applique un tri ['name'=>'asc']
        // a noter que findBy([]) avec les crochets vides est l'équivalent dans findAll()

        return $this->render(
            'admin/category/index.html.twig',
            [
                'categories' => $categories
            ]
        );
    }

    /**
     * 2° Methode qui permet de créer/modifier des catégories en back office
     * {id} est optionnel et doit etre un nombre
     * @Route("/edition/{id}", defaults={"id": null}, requirements={"id":"\d+"})
     */
    public function edit(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        if(is_null($id)){ // création d'une catégorie
            $category = new Category();
        }else{ // modification d'une catégorie
            $category = $em->find(Category::class, $id);

            // 404 si l'id recu dans l'url n'est pas en bdd
            if(is_null($category)){
                throw new NotFoundHttpException();
            }
        }



        // Création d'un form lié à la catégorie
        $form = $this->createForm(CategoryType::class, $category);

        // le formulaire analyse la requete HTTP et traitre le formulaire s'il a été soumis
        $form->handleRequest($request);

        // si le formulaire a été envoyé
        if($form->isSubmitted()){
            dump($category);

            // si les validations à partir des annotations dans l'entité Category sont OK
            if($form->isValid()){
                // enregistrement de la catégorie en bdd
                $em->persist($category);
                $em->flush();

                // message de confirmation
                $this->addFlash('success', 'La catégorie est bien enregistrée');
                // redirection vers la liste apres enregistrement
                return $this->redirectToRoute('app_admin_category_index');
            } else{
                $this->addFlash('error', 'Le formulaire contient des erreurs');
            }
        }

        return $this->render(
            'admin/category/edit.html.twig',
            [
                // passage du formulaire au template
                'form' => $form->createView()
            ]
        );
    }

    /**
     * @Route("/suppression/{id}")
     */
    public function delete(Category $category) // param converter
    {
        $em = $this->getDoctrine()->getManager();

        $em->remove($category);
        $em->flush();

        $this->addFlash(
            'success',
            'La catégorie est supprimée'
        );

        return $this->redirectToRoute('app_admin_category_index');
    }
}