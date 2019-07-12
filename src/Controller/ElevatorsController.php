<?php

namespace App\Controller;

use App\Entity\Calls;
use App\Entity\Elevators;
use App\Form\ElevatorsType;
use App\Repository\ElevatorsRepository;
use Doctrine\ORM\EntityManager;
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
        return $this->render('elevators/show.html.twig', [
            'elevator' => $elevator,
            'call_statistic' => $this->getCallStatistic($elevator),
            'move_statictic' => $this->getMoveStatistic($elevator),
        ]);
    }

    private function getCallStatistic(Elevators $elevator)
    {
        $em = $this->getEntityManager();
        $sql = 'select floor_to, count(floor_to) from calls
 where elevator_id= ' . $elevator->getId() . ' and status= ' . Calls::CALL_FINISHED . ' 
 group by floor_to
 ;';
        $statement = $em->getConnection()->prepare($sql);
        $statement->execute();
        return $statement->fetchAll();
    }

    private function getEntityManager(): EntityManager
    {
        return $this->container->get('doctrine')->getEntityManager();
    }

    private function getMoveStatistic(Elevators $elevator)
    {
        $em = $this->getEntityManager();
        $sql = 'select * from calls
 where elevator_id= ' . $elevator->getId() . ' and status= ' . Calls::CALL_FINISHED . ' 
order by id
 ;';
        $statement = $em->getConnection()->prepare($sql);
        $statement->execute();
        $calls = $statement->fetchAll();

        $res = [];
        $moving = [];
        foreach ($calls as $v) {
            $moving[] = $v['floor_from'];
        }
        return $this->sliceDirection($moving);

    }

    public function sliceDirection($calls)
    {
        $ob = [];
        $res = [];
        $direction = $calls[0] < $calls[1] ? 'up' : 'down';
        $is_direction_changed = false;
        for ($i = 0; $i < count($calls) - 1; $i++) {

            if ($direction == 'up' && $calls[$i] > $calls[$i + 1]) {
                $is_direction_changed = true;
                $direction = 'down';
            }
            if ($direction == 'down' && $calls[$i] < $calls[$i + 1]) {
                $is_direction_changed = true;
                $direction = 'up';
            }
            $ob[] = $calls[$i];

            if ($is_direction_changed) {
                $res[] = implode(" -> ", $ob);
                $is_direction_changed = false;
                $ob = [];
                $ob[] = $calls[$i];
            }
            if ($i == count($calls) - 2) {
                if ($is_direction_changed) {
                    $res[] = implode(" -> ", $ob);
                    $ob = [];
                    $ob[] = $calls[$i];
                    $ob[] = $calls[$i + 1];
                    $res[] = implode(" -> ", $ob);
                } else {
                    $ob[] = $calls[$i + 1];
                    $res[] = implode(" -> ", $ob);
                }

            }
        }
        return $res;
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
