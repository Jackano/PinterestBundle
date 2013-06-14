<?php

namespace Zeldanet\PinterestBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Zeldanet\PinterestBundle\Entity\Pins;

class DefaultController extends Controller
{
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        // Get Pins
        $pins = array();
        $query = $em->createQuery(
                'SELECT p FROM ZeldanetPinterestBundle:Pins p WHERE 1=1 ORDER BY p.actions DESC '
        )
            ->setMaxResults(10)
        ;

        $pins = $query->getResult();

        return $this->render('ZeldanetPinterestBundle:Default:index.html.twig', array(
            'pins' => $pins,

            )
        );
    }

    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $pin = $em->getRepository('ZeldanetPinterestBundle:Pins')
            ->find($id);

        if (!$pin) {
            throw $this->createNotFoundException(
                'No pin found for id '.$id
            );
        }

        return $this->render('ZeldanetPinterestBundle:Default:show.html.twig', array(
            'pin' => $pin,

            )
        );
    }
}
