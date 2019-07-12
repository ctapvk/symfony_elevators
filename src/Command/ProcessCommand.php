<?php

namespace App\Command;

use App\Entity\Calls;
use App\Entity\Elevators;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Query\ResultSetMapping;
use Grpc\Call;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class ProcessCommand extends ContainerAwareCommand
{
    protected static $defaultName = 'app:process';

    protected function configure()
    {
        $this
            ->setDescription('Add a short description for your command')
            ->addArgument('arg1', InputArgument::OPTIONAL, 'Argument description')
            ->addOption('option1', null, InputOption::VALUE_NONE, 'Option description');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        $arg1 = $input->getArgument('arg1');

        if ($arg1) {
            $io->note(sprintf('You passed an argument: %s', $arg1));
        }

        if ($input->getOption('option1')) {
            // ...
        }
        $em = $this->getManager();

        while (true) {
            echo date("U") . "test \n";
            $res = $em->getRepository('App:Calls')->createQueryBuilder('c')
                ->andWhere('c.status = :val')
                ->setParameter('val', Calls::CALL_STARTED)
                ->orderBy('c.id', 'ASC')
                ->getQuery()
                ->getResult();

            $res = array_map(function (Calls $call) use ($em) {
                $elevator = $call->getElevator();
                $diff = ($elevator->getPosition() - $call->getFloorTo());
                $direction = ($diff < 0) ? 1 : -1;
                $position = $elevator->getPosition();
                echo $elevator->getName() . " $direction  $position \n";
                if ($diff == 0) {
                    $elevator->setStatus(Elevators::ELEVATOR_IDLE);
                    $call->setStatus(Calls::CALL_FINISHED);
                    $em->persist($call);
                } else {
                    $position = $elevator->getPosition() + $direction;
                    $elevator->setPosition($position);
                }
                $em->persist($elevator);
            }, $res);
            $em->flush();
            sleep(2);
        }

        $io->success('You have a new command! Now make it your own! Pass --help to see your options.');
    }

    private function getManager(): EntityManager
    {
        return $this->getContainer()->get('doctrine.orm.entity_manager');
    }

    private function moveIdleToFirstFloor()
    {
        $em = $this->getManager();
        $sql = 'SELECT h.*, e.* FROM houses as h
 join elevators as e on h.id = e.house_id
 where e.status=1 and e.position!=1
 ;';
        $statement = $em->getConnection()->prepare($sql);
        $statement->execute();
        $res = $statement->fetchAll();
        print_r($res);

//        $res = $em->getRepository('App:Elevators')->createQueryBuilder('c')
//            ->andWhere('c.status = :val')
//            ->setParameter('val', Elevators::ELEVATOR_IDLE)
//            ->orderBy('c.id', 'ASC')
//            ->getQuery()
//            ->getResult();
//        $res = array_map(function (Elevators $elevator) use ($em) {
//            $call = $elevator->getCalls();
//            echo $elevator->getName() . "  \n";
//
//        }, $res);
    }
}
