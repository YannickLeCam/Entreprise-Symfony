<?php

namespace App\Controller;


use App\Entity\Employe;
use App\Entity\Entreprise;
use App\Form\EmployeType;
use App\Repository\EmployeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class EmployeController extends AbstractController
{
    #[Route('/employe', name: 'app_employe')]
    public function index(EmployeRepository $employeRepository): Response
    {
        $employes = $employeRepository->findBy([] , ['nom' => 'ASC']);
        return $this->render('employe/index.html.twig', [
            'employes' => $employes
        ]);
    }

    #[Route('/employeDetail/{id}', name: 'app_employeDetail', requirements : ['id'=>'\d+'])]
    public function employeDetail(EmployeRepository $employeRepository, int $id): Response
    {
        $employe = $employeRepository->findOneBy(['id'=>$id] , ['nom' => 'ASC']);
        return $this->render('employe/detail.html.twig', [
            'employe' => $employe
        ]);
    }

    #[Route('/employeDetail/new', name: 'app_employeNew')]
    #[Route('/employeDetail/edit-{id}', name: 'app_employeEdit',requirements : ['id'=>'\d+'])]
    public function new_edit(Employe $employe=null, Request $request,EntityManagerInterface $em) :Response{

        if (!$employe) {
            $employe= new Employe();
        }

        $form = $this->createForm(EmployeType::class , $employe);

        $form -> handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            
            $newEmploye = $form->getData();

            $em->persist($newEmploye);
            $em->flush();
            $this->addFlash('success',"L'employé a bien été ajouté");
            return $this->redirectToRoute('app_employe');
        }
        return $this->render('employe/new.html.twig', [
            'form' => $form,
            'edit'=>$employe->getId(),
        ]);
    }

    #[Route('/employeDetail/delete-{id}', name: 'app_employeDelete',requirements : ['id'=>'\d+'])]
    public function delete(Employe $employe,EntityManagerInterface $em) : Response {
        $em->remove($employe);
        $em->flush();
        $this->addFlash('success','Vous avez bien supprimé un employe');
        return $this->redirectToRoute('app_employe');
    }

}
