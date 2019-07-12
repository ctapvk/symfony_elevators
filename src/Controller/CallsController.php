<?php

namespace App\Controller;

use App\Entity\Calls;
use App\Entity\Elevators;
use App\Entity\Houses;
use App\Form\CallsType;
use App\Repository\CallsRepository;
use Doctrine\DBAL\LockMode;
use Doctrine\ORM\EntityManager;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/calls")
 */
class CallsController extends AbstractController
{

    /**
     * @param Houses $house
     * @param int $floor
     */
    function createNewCall(Houses $house, int $floor, EntityManager $em)
    {
        $elevators = $house->getElevators();
        $free_elevators = array_filter($elevators->getValues(), function (Elevators $el) {
            return $el::ELEVATOR_IDLE == $el->getStatus();
        });
        if (count($free_elevators) == 0) {
            return false;
        }
        $elevator = $this->findClosestElevator(array_values($free_elevators), $floor);
        $em->beginTransaction();
        $em->lock($elevator, LockMode::PESSIMISTIC_READ);
        $elevator->setStatus(Elevators::ELEVATOR_MOVING);
        $em->persist($elevator);

        $call = new Calls();
        $call->setElevator($elevator);
        $call->setFloorFrom($elevator->getPosition());
        $call->setFloorTo($floor);
        $call->setStatus(Calls::CALL_STARTED);
        $em->persist($call);

        $house->setLastCallAt(new \DateTimeImmutable());
        $em->persist($house);

        $em->flush();
        $em->lock($elevator, LockMode::NONE);
        $em->commit();
        $em->clear();
        return $elevator;
    }


    private function findClosestElevator(array $ar, int $floor): Elevators
    {
        $diff = [];
        foreach ($ar as $v) {
            $diff[] = abs($v->getPosition() - $floor);
        }
        return $ar[array_search(min($diff), $diff)];
    }

    /**
     * @Route("/", name="calls_index", methods={"GET"})
     */
    public function index(Request $request, PaginatorInterface $paginator): Response
    {

        $em = $this->container->get('doctrine')->getEntityManager();
        $dql = "SELECT a FROM  App\Entity\Calls a order by a.id desc";
        $query = $em->createQuery($dql);

        $pagination = $paginator->paginate(
            $query, /* query NOT result */
            $request->query->getInt('page', 1), /*page number*/
            10 /*limit per page*/
        );

        return $this->render('calls/index.html.twig', [
            'pagination' => $pagination,
        ]);
    }

    /**
     * @Route("/new", name="calls_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $call = new Calls();
        $form = $this->createForm(CallsType::class, $call);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($call);
            $entityManager->flush();

            return $this->redirectToRoute('calls_index');
        }

        return $this->render('calls/new.html.twig', [
            'call' => $call,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="calls_show", methods={"GET"})
     */
    public function show(Calls $call): Response
    {
        return $this->render('calls/show.html.twig', [
            'call' => $call,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="calls_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Calls $call): Response
    {
        $form = $this->createForm(CallsType::class, $call);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('calls_index', [
                'id' => $call->getId(),
            ]);
        }

        return $this->render('calls/edit.html.twig', [
            'call' => $call,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="calls_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Calls $call): Response
    {
        if ($this->isCsrfTokenValid('delete' . $call->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($call);
            $entityManager->flush();
        }

        return $this->redirectToRoute('calls_index');
    }
}
