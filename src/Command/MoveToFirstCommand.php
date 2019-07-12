<?php

namespace App\Command;

use App\Controller\CallsController;
use App\Entity\Houses;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class MoveToFirstCommand extends ContainerAwareCommand
{
    protected static $defaultName = 'app:move-to-first';

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
        $callsContorller = new CallsController();

        while (true) {
            $time = (new \DateTimeImmutable())->modify('-5 minutes')->format("Y-m-d H:i:s");

            $res = $em->getRepository('App:Houses')->createQueryBuilder('c')
                ->andWhere('c.last_call_at < :val')
                ->setParameter('val', $time)
                ->orderBy('c.id', 'ASC')
                ->getQuery()
                ->getResult();

            $res = array_map(function (Houses $house) use ($em, $callsContorller) {
                echo date("U") . "test \n";
                $need_to_call = true;
                foreach ($house->getElevators()->getValues() as $v) {
                    if ($v->getPosition() == 1) $need_to_call = false;
                }
                if ($need_to_call) {
                    $res = $callsContorller->createNewCall($house, 1, $em);
                    if ($res) {
                        $name = $res->getName();
                        echo "called elevator $name to first floor";
                    }
                }
            }, $res);
            $em->flush();
            $em->clear();
            sleep(30);
        }

        $io->success('You have a new command! Now make it your own! Pass --help to see your options.');
    }


    private function getManager(): EntityManager
    {
        return $this->getContainer()->get('doctrine.orm.entity_manager');
    }
}
