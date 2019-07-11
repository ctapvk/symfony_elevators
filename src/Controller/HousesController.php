<?php

namespace App\Controller;

use App\Entity\Houses;
use App\Form\HousesType;
use App\Repository\HousesRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/houses")
 */
class HousesController extends AbstractController
{
    /**
     * @Route("/", name="houses_index", methods={"GET"})
     */
    public function index(Request $request, PaginatorInterface $paginator): Response
    {
        $em = $this->container->get('doctrine')->getEntityManager();
        $dql = "SELECT a FROM  App\Entity\Houses a order by a.id desc";
        $query = $em->createQuery($dql);

        $pagination = $paginator->paginate(
            $query, /* query NOT result */
            $request->query->getInt('page', 1), /*page number*/
            10 /*limit per page*/
        );

        // parameters to template
        return $this->render('houses/index.html.twig', [
            'pagination' => $pagination,
        ]);
    }

    /**
     * @Route("/new", name="houses_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $house = new Houses();
        $form = $this->createForm(HousesType::class, $house);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($house);
            $entityManager->flush();

            return $this->redirectToRoute('houses_index');
        }

        return $this->render('houses/new.html.twig', [
            'house' => $house,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="houses_show", methods={"GET","POST"})
     */
    public function show(Houses $house, Request $request): Response
    {
        $elevetors = $house->getElevators()->getValues();
        usort($elevetors, function ($el1, $el2) {
            return $el1->getId() > $el2->getId();
        });
        return $this->render('houses/show.html.twig', [
            'house' => $house,
            'elevators' => $elevetors,
        ]);
    }

    /**
     * @Route("/{id}/call", name="houses_call", methods={"POST"})
     */
    public function callAction(Houses $house, Request $request): Response
    {
        if ($floor = $request->get('floor')) {
            $this->addFlash('success', "Вы вызвали лифт на $floor этаж");
        }
        return $this->redirectToRoute('houses_show', ['id' => $house->getId()]);
    }

    /**
     * @Route("/{id}/edit", name="houses_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Houses $house): Response
    {
        $form = $this->createForm(HousesType::class, $house);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('houses_index', [
                'id' => $house->getId(),
            ]);
        }

        return $this->render('houses/edit.html.twig', [
            'house' => $house,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="houses_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Houses $house): Response
    {
        if ($this->isCsrfTokenValid('delete' . $house->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($house);
            $entityManager->flush();
        }

        return $this->redirectToRoute('houses_index');
    }
}
