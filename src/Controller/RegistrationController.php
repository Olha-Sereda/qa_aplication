<?php
/**
 * Registration controller.
 */

namespace App\Controller;

use App\Entity\User;
use App\Form\HomeType;
use App\Form\Type\RegistrationFormType;
use App\Service\RegistrationService;
use App\Service\UserService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class RegistrationController.
 */
class RegistrationController extends AbstractController
{

    /**
     * User service.
     */
    private UserService $userService;

    /**
     * Registration service.
     */
    private RegistrationService $registrationService;

    /**
     * RegistrationController constructor.
     *
     * @param \App\Service\RegistrationService $registrationService Registration service
     * @param \App\Service\UserService         $userService         User service
     */
    public function __construct(RegistrationService $registrationService, UserService $userService)
    {
        $this->registrationService = $registrationService;
        $this->userService = $userService;
    }

    /**
     * Create action.
     *
     * @param Request $request HTTP request
     *
     * @return Response HTTP response
     *
     * @throws \Doctrine\ORM\ORMException
     */
    #[Route('/register', name: 'app_register', methods: 'GET|POST')]
    public function create(Request $request): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class);
        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            if ($this->userService->findOneByEmail($data['email']) !== null) {
                $this->addFlash('danger', 'message_email_already_exists');

                return $this->redirectToRoute('app_register');
            }

            $this->registrationService->register($data, $user);
            $this->addFlash('success', 'message_registered_successfully');

            //return $this->redirectToRoute('question_index');
        }

        return $this->render(
            'registration/index.html.twig',
            [
                'form' => $form->createView(),
            ]
        );
    }
}