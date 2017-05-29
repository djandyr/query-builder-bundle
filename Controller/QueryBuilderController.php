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
        setcookie('school', 1);
        setcookie('EDUCTIVEAUTH', 'eyJ0eXAiOi');
        return $this->render('LittlerobinsonQueryBuilderBundle:QueryBuilder:index.html.twig');
    }

    /**
     * @Method("POST")
     * @return Response
     */
    public function queryAction()
    {
        RunQueryBuilder::execute();
        return new Response();
    }
}
