<?php
/**
 * Hello controller.
 */

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * Class HelloController.
 */

class HelloController extends AbstractController
{
    /**
     * Index action.
     *
     * @param string $name User input
     *
     * @return Response HTTP response
     */
    #[Route('/hello/{name}', name: 'hello_index',  requirements: ['name' => '[a-zA-Z]+'],  defaults: ['name' => 'World'],  methods: 'GET' )]
    public function index(string $name): Response
    {
        return $this->render(
            'hello/index.html.twig',
            ['name' => $name]
        );
    }
}
