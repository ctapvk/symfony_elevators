<?php

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class MoveToFirstCommand extends Command
{
    protected static $defaultName = 'app:move-to-first';

    protected function configure()
    {
        $this
            ->setDescription('Add a short description for your command')
            ->addArgument('arg1', InputArgument::OPTIONAL, 'Argument description')
            ->addOption('option1', null, InputOption::VALUE_NONE, 'Option description')
        ;
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
        while (true) {
            $time =  (new \DateTimeImmutable())->modify('-5 minutes')->format("Y-m-d H:i:s");

            echo date("U") . "test $time \n";

//            $res = $em->getRepository('App:Houses')->createQueryBuilder('c')
//                ->andWhere('c.updated_at = :val')
//                ->setParameter('val',)
//                ->orderBy('c.id', 'ASC')
//                ->getQuery()
//                ->getResult();
//
//            $res = array_map(function (Calls $call) use ($em) {
//                $elevator = $call->getElevator();
//                $diff = ($elevator->getPosition() - $call->getFloorTo());
//                $direction = ($diff < 0) ? 1 : -1;
//                $position = $elevator->getPosition();
//                echo $elevator->getName() . " $direction  $position \n";
//                if ($diff == 0) {
//                    $elevator->setStatus(Elevators::ELEVATOR_IDLE);
//                    $call->setStatus(Calls::CALL_FINISHED);
//                    $em->persist($call);
//                } else {
//                    $position = $elevator->getPosition() + $direction;
//                    $elevator->setPosition($position);
//                }
//                $em->persist($elevator);
//            }, $res);
//            $em->flush();
            sleep(30);
        }

        $io->success('You have a new command! Now make it your own! Pass --help to see your options.');
    }


    private function getManager(): EntityManager
    {
        return $this->getContainer()->get('doctrine.orm.entity_manager');
    }
}
