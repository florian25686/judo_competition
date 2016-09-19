<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;


/**
 * Controller für die Erstellung und Verwaltung von Wettkämpfern
 * 
 */
class FighterController extends Controller
{
    /**
     * @Route("/fighter", name="fighterIndex")
     */
    public function indexAction(Request $request)
    {
        // replace this example code with whatever you need
        return $this->render('fighter/index.html.twig', [
            'base_dir' => realpath($this->getParameter('kernel.root_dir').'/..'),
        ]);
    }
    
    /**
     * TODO: Create the form, block the groupaddition if someone is already in a group
     * @Route("/fighter/createFighter", name="createFighter")
     * @param Request $request
     */
    public function createFighterAction(Request $request) 
    {
        $em = $this->getDoctrine()->getManager();
        
        return $this->render('fighter/createFighter.html.twig', [
        	
	]);
    }
}
