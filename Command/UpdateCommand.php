<?php
// src/Zeldanet/PinterestBundle/Command/UpdateCommand.php

namespace Zeldanet\PinterestBundle\Command;

/*
 * 	USAGE
 * 	$ php app/console pinterest:update
 * 	@todo params user, board
 */

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DomCrawler\Crawler;
use Zeldanet\PinterestBundle\Entity\Pins;

class UpdateCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
                ->setName('pinterest:update')
                ->setDescription('Update pins')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $em = $this->getContainer()->get('doctrine')->getManager();

        $total = 0;

        // Get PINs URLs
        $pins = array();
        $query = $em->createQuery(
                'SELECT p FROM ZeldanetPinterestBundle:Pins p WHERE 1=1 ORDER BY p.updated ASC'
        )
            ->setMaxResults(100)
        ;

        $pins = $query->getResult();

        // Look up and register pins
        foreach ($pins as $pin) {
            $user = $pin->getAuthor();
            $board = $pin->getBoard();
            $userboard = $user . '/' . $board;
            $userboard = trim($userboard, '/');

            $id = $pin->getId();
            $uri = $pin->getUri();
            $url = $pin->getUrl();

            $html = @file_get_contents($url);
            if(! $html) 	continue;

            $crawler = new Crawler($html);
               if(! is_object($crawler)) 	continue;

            // Retrieve pinner and board
            $tmp_url = $crawler->filter('meta[property="pinterestapp:pinboard"]')->extract('content');
            $tmp_url = (string) array_shift($tmp_url);
            $tmp_url = trim($tmp_url, '/');
            $tmp_url = explode('/', $tmp_url);

            if (count($tmp_url)>3) {
                $user = $tmp_url[3];
                $board = $tmp_url[4];
            }

            $pin->hydrateWithCrawler($crawler, array( 'id'=>$id, 'url'=>$url, 'uri'=>$uri, 'user'=>$user, 'board'=>$board ));

            $em->persist($pin);

            $em->flush();
            $total++;
        }

        $output->writeln('Pinterest bundle updated: ' . $total . ' pins~ ');
    }

}
