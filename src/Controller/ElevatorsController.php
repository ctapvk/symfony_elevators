<?php

namespace App\Controller;

use App\Entity\Elevators;
use App\Form\ElevatorsType;
use App\Repository\ElevatorsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/elevators")
 */
class ElevatorsController extends AbstractController
{
    /**
     * @Route("/", name="elevators_index", methods={"GET"})
     */
    public function index(ElevatorsRepository $elevatorsRepository): Response
    {
        return $this->render('elevators/index.html.twig', [
            'elevators' => $elevatorsRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="elevators_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $elevator = new Elevators();
        $form = $this->createForm(ElevatorsType::class, $elevator);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($elevator);
            $entityManager->flush();

            return $this->redirectToRoute('elevators_index');
        }

        return $this->render('elevators/new.html.twig', [
            'elevator' => $elevator,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="elevators_show", methods={"GET"})
     */
    public function show(Elevators $elevator): Response
    {
        $em = $this->container->get('doctrine')->getEntityManager();
        $sql = 'select floor_to, count(floor_to) from calls
 where elevator_id= ' . $elevator->getId() . ' 
 group by floor_to
 ;';
        $statement = $em->getConnection()->prepare($sql);
        $statement->execute();
        $res = $statement->fetchAll();

        return $this->render('elevators/show.html.twig', [
            'elevator' => $elevator,
            'call_statistic' => $res,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="elevators_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Elevators $elevator): Response
    {
        $form = $this->createForm(ElevatorsType::class, $elevator);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('elevators_index', [
                'id' => $elevator->getId(),
            ]);
        }

        return $this->render('elevators/edit.html.twig', [
            'elevator' => $elevator,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="elevators_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Elevators $elevator): Response
    {
        if ($this->isCsrfTokenValid('delete' . $elevator->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($elevator);
            $entityManager->flush();
        }

        return $this->redirectToRoute('elevators_index');
    }
}
