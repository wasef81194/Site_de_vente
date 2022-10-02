<?php

namespace App\Controller;

use App\Entity\Produit;
use App\Entity\Image;
use App\Form\ProduitType;
use App\Repository\ProduitRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * @Route("/produit")
 */
class ProduitController extends AbstractController
{
    /**
     * @Route("/", name="produit_index", methods={"GET"})
     */
    public function index(ProduitRepository $produitRepository): Response
    {
        return $this->render('produit/index.html.twig', [
            'produits' => $produitRepository->findProductsNoDelete(),
        ]);
    }

    /**
     * @Route("/new", name="produit_new", methods={"GET", "POST"})
     */
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $produit = new Produit();
        $form = $this->createForm(ProduitType::class, $produit);
        $form->handleRequest($request);
        
       
        if ($form->isSubmitted() && $form->isValid()) {
            //récuperer les images
            $images = $form['images']->getData();
           
            dump($images);
            //on parcours chaque images 

            foreach($images as $image){
                $fichier = md5(uniqid()).'.'.$image->guessExtension();

                //on copie les fichier dans le dossier uploads
                $image->move($this->getParameter('image_directory'),$fichier);

                // on stock l'image dans la bdd
                $img = new Image();
                $img->setNom($fichier);
                $produit->addImage($img); 
                
            }
            $produit->setDate(new \DateTime('@'.strtotime('now')));
            $entityManager->persist($produit);// mettre cascase dans la variable images 
            $entityManager->flush();

            return $this->redirectToRoute('produit_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('produit/new.html.twig', [
            'produit' => $produit, 
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="produit_show", methods={"GET"})
     */
    public function show(Request $request,Produit $produit): Response
    {
        return $this->render('produit/show.html.twig', [
            'produit' => $produit,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="produit_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, Produit $produit, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ProduitType::class, $produit);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            //récuperer les images
            $images = $form['images']->getData();
           
            dump($images);
            //on parcours chaque images 

            foreach($images as $image){
                $fichier = md5(uniqid()).'.'.$image->guessExtension();

                //on copie les fichier dans le dossier uploads
                $image->move($this->getParameter('image_directory'),$fichier);

                // on stock l'image dans la bdd
                $img = new Image();
                $img->setNom($fichier);
                $produit->addImage($img); 
                
            }
            $entityManager->persist($produit);// mettre cascase dans la variable images 
            $entityManager->flush();

            return $this->redirectToRoute('produit_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('produit/edit.html.twig', [
            'produit' => $produit,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="produit_delete", methods={"POST"})
     */
    public function delete(Request $request, Produit $produit, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$produit->getId(), $request->request->get('_token'))) {
           /* foreach($produit->getImages() as $image){
                $produit->removeImage($image);
                unlink($this->getParameter('image_directory').'/'.$image->getNom());
                $entityManager->remove($image);
            }*/
            $produit->setDateDelete(new \DateTime('@'.strtotime('now')));
            $entityManager->persist($produit);
            $entityManager->flush();
        }

        return $this->redirectToRoute('produit_index', [], Response::HTTP_SEE_OTHER);
    }

    /**
    * @Route("/delete/image/{id}", name="produit_image_delete", methods={"DELETE"})
     */
    public function deleteImage(Image $image,Request $request): Response
    {
        $data = json_decode($request->getContent(), true);

        // On vérifie si le token est valide
        if($this->isCsrfTokenValid('delete'.$image->getId(), $data['_token'])){
            // On récupère le nom de l'image
            $nom = $image->getNom();
            // On supprime le fichier
            unlink($this->getParameter('image_directory').'/'.$nom);

            // On supprime l'entrée de la base
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($image);
            $entityManager->flush();

            // On répond en json
           // dump('sucess');
            return new JsonResponse(['success' => 1]);
        }else{
            return new JsonResponse(['error' => 'Token Invalide'], 400);
        }
    }
}
