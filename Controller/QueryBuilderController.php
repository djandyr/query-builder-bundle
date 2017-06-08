<?php

namespace Littlerobinson\QueryBuilderBundle\Controller;

use Littlerobinson\QueryBuilderBundle\Utils\RunQueryBuilder;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class QueryBuilderController extends Controller
{
    /**
     * @return Response
     */
    public function indexAction()
    {
        return $this->render('LittlerobinsonQueryBuilderBundle:QueryBuilder:query_layout.html.twig', array(
            'queryPath' => '/querybuilder/query'
        ));
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
