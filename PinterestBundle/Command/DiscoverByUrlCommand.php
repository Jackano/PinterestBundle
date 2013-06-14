<?php
// src/Zeldanet/PinterestBundle/Command/DiscoverByUrlCommand.php

namespace Zeldanet\PinterestBundle\Command;

/*
 * 	USAGE
 * 	$ php app/console pinterest:discoverbyurl
 * 	@todo params user, board
 */

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DomCrawler\Crawler;
use Zeldanet\PinterestBundle\Entity\Pins;

class DiscoverByUrlCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
                ->setName('pinterest:discoverbyurl')
                ->setDescription('Discover pins by Url')
                ->addArgument('url', InputArgument::REQUIRED, 'Which url do you want to discoverByUrl pins?  eg.: http://pinterest.com/zeldanet/zelda-fans/?page=1 ')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $em = $this->getContainer()->get('doctrine')->getManager();

        $url = $input->getArgument('url');

        if ($url) {
            $text = 'Discover by Url ' . $url;
        } else {
            $text = 'Url?';
        }

        $user = "";
        $board = "";

        $total = 0;

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

        $output->writeln('Pinterest bundle discovered by Url: ' . $total . ' pins~ ');
    }

}
