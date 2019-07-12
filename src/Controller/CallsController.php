<?php

namespace App\Controller;


use App\Entity\Calls;
use App\Entity\Elevators;
use App\Entity\Houses;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\DBAL\LockMode;
use Doctrine\ORM\EntityManager;


class CallsController
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
        $em->lock($elevator, LockMode::PESSIMISTIC_WRITE);
        $elevator->setStatus(Elevators::ELEVATOR_MOVING);
        $em->persist($elevator);

        $call = new Calls();
        $call->setElevator($elevator);
        $call->setFloorFrom($elevator->getPosition());
        $call->setFloorTo($floor);
        $call->setStatus(Calls::CALL_STARTED);
        $em->persist($call);

        $em->flush();
        $em->commit();
        return $elevator;
    }


    function findClosestElevator(array $ar, int $floor): Elevators
    {
        $diff = [];
        foreach ($ar as $v) {
            $diff[] = abs($v->getPosition() - $floor);
        }
        return $ar[array_search(min($diff), $diff)];
    }
}
