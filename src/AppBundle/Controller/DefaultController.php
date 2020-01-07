<?php

namespace AppBundle\Controller;

use AppBundle\Entity\ShopifyOrder;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends Controller
{


    protected function getRepository($class)
    {
        $em = $this->getDoctrine()->getManager();
        $repo = $em->getRepository('AppBundle:' . $class);
        return $repo;
    }

    protected function insert($object)
    {
        $em = $this->getDoctrine()->getManager();
        $em->persist($object);
        $em->flush();
    }

    protected function remove($object)
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($object);
        $em->flush();
    }


}
