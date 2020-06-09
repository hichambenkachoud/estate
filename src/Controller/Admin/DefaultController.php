<?php


namespace App\Controller\Admin;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends AbstractController
{

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/", name="backend_index", methods={"GET"})
     */
    public function index()
    {
        return $this->render('backend/index.html.twig');
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/", name="backend_language", methods={"POST"})
     */
    public function language(Request $request)
    {   //$routeName = $request->get('_current_route');
        return $this->redirectToRoute("backend_index");
    }
}
