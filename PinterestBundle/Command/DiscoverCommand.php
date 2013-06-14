<?php
// src/Zeldanet/PinterestBundle/Command/DiscoverCommand.php

namespace Zeldanet\PinterestBundle\Command;

/*
 * 	USAGE
 * 	$ php app/console pinterest:discover
 * 	@todo params user, board
 */

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DomCrawler\Crawler;
use Zeldanet\PinterestBundle\Entity\Pins;

class DiscoverCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
                ->setName('pinterest:discover')
                ->setDescription('Discover pins')
                ->addArgument('user', InputArgument::REQUIRED, 'Which user do you want to discover pins?  eg.: zeldanet ')
                ->addArgument('board', InputArgument::REQUIRED, 'Which board do you want to discover pins?  eg.: zelda-fans ')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $em = $this->getContainer()->get('doctrine')->getManager();

        $user = $input->getArgument('user');
        $board = $input->getArgument('board');

        $userboard = $user . '/' . $board;
        if ($userboard) {
            $text = 'Hello userboard ' . $userboard;
        } else {
            $text = 'Hello?';
        }

        $userboard = trim($userboard, '/');

        $date = date( "Y-m-d" );
        $total = 0;

        $url = "http://pinterest.com/$userboard/";
        $html = file_get_contents($url);
        $crawler = new Crawler($html);
        $crawler = $crawler->filter('body');

        // Find Pins URLs
        // ex.: <a href="/pin/52917364344226652/" class="PinImage ImgLink">
        $crawler = $crawler->filter('a.pinImageWrapper, a.PinImage, a.ImgLink');

        $hrefs = array();
        array_push($hrefs, $crawler->extract(array('href')));

        // Filter PINs URLs
        $pins = array();
        foreach ($hrefs[0] as $href) {
            if( preg_match('~pin/[0-9]+~si', $href ) )
                array_push($pins, $href );
        }

        // Look up and register pins
        foreach ($pins as $uri) {
            $id = preg_replace('~^.*pin\/([0-9]+)[^0-9]*$~si', "$1", $uri);

            $pin = $em->getRepository('ZeldanetPinterestBundle:Pins')
                ->find($id);

            if (!$pin) {
                $pin = new Pins();
            } else {
                continue;
            }

            $url = "http://pinterest.com" . $uri;
            $html = file_get_contents($url);
            $crawler = new Crawler($html);

            $pin->hydrateWithCrawler($crawler, array( 'id'=>$id, 'url'=>$url, 'uri'=>$uri, 'user'=>$user, 'board'=>$board, 'date'=>$date ));

            $em->persist($pin);

            $em->flush();
            $total++;
        }

        $output->writeln('Pinterest bundle discovered: ' . $total . ' pins~ ');
    }

}
