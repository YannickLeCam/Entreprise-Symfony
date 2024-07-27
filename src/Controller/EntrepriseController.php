<?php

namespace App\Controller;


use App\Entity\Entreprise;
use App\Form\EntrepriseType;
use App\Repository\EmployeRepository;
use App\Repository\EntrepriseRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

class EntrepriseController extends AbstractController
{
    #[Route('/entreprise', name: 'app_entreprise')]
    public function index(EntityManagerInterface $entityManager): Response
    {   

        $entreprises = $entityManager->getRepository(Entreprise::class)->findBy([], ['raisonSociale'=>'ASC']);
        return $this->render('entreprise/index.html.twig', [
            'controller_name' => 'EntrepriseController',
            'entreprises' => $entreprises
        ]);
    }

    #[Route('/entrepriseDetail/{id}', name: 'app_entrepriseDetail', requirements : ['id'=>'\d+'])]
    public function employeDetail(EntrepriseRepository $entrepriseRepository, int $id): Response
    {
        $entreprise = $entrepriseRepository->findOneBy(['id'=>$id]);
        
        return $this->render('entreprise/detail.html.twig', [
            'entreprise' => $entreprise
        ]);
    }
    #[Route('/entrepriseDetail/new', name: 'app_entrepriseNew')]
    #[Route('/entrepriseDetail/edit-{id}', name: 'app_entrepriseEdit',requirements : ['id'=>'\d+'])]
    public function new(Entreprise $entreprise=null,Request $request,EntityManagerInterface $em) :Response{
        
        if (!$entreprise) {
            $entreprise = new Entreprise();
        }

        $form = $this->createForm(EntrepriseType::class , $entreprise);

        $form -> handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            
            $newEntreprise = $form->getData();

            $em->persist($newEntreprise);
            $em->flush();
            $this->addFlash('success',"L'entreprise a bien été ajouté");
            return $this->redirectToRoute('app_entreprise');
        }
        return $this->render('entreprise/new.html.twig', [
            'form' => $form,
            'edit' => $entreprise->getId(),
        ]);
    }
    #[Route('/entrepriseDetail/delete-{id}', name: 'app_entrepriseDelete',requirements : ['id'=>'\d+'])]
    public function delete(Entreprise $entreprise,EntityManagerInterface $em) : Response {
        $em->remove($entreprise);
        $em->flush();
        $this->addFlash('success','Vous avez bien supprimé une entreprise');
        return $this->redirectToRoute('app_entreprise');
    }

}
