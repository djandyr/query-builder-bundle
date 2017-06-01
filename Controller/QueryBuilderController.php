<?php

namespace Littlerobinson\QueryBuilderBundle\Controller;

use Littlerobinson\QueryBuilderBundle\Utils\RunQueryBuilder;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Response;

class QueryBuilderController extends Controller
{
    /**
     * @return Response
     */
    public function indexAction()
    {
        $cookie   = new Cookie('school', 1);
        $response = new Response();
        $response->headers->setCookie($cookie, time() + 3600);
        $response->setContent($this->container->get('twig')->render('LittlerobinsonQueryBuilderBundle:QueryBuilder:query_layout.html.twig', []));
        return $response;
    }

    /**
     * @Method("POST")
     * @return Response
     */
    public function queryAction()
    {
        $run = RunQueryBuilder::getInstance($this->container);
        $run->execute();
        return new Response();
    }
}
