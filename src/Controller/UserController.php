<?php
/**
 * User controller.
 */

namespace App\Controller;

use App\Entity\User;
use App\Form\Type\UserType;
use App\Controller\IsGranted;
use App\Form\Type\PasswordType;
use App\Service\UserServiceInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

/**
 * Class UserController.
 */
#[Route('/user')]
class UserController extends AbstractController
{
    /**
     * Password hasher.
     */
    private UserPasswordHasherInterface $passwordHasher;

    /**
     * User service.
     */
    private UserServiceInterface $userService;

    /**
     * Translator.
     */
    private TranslatorInterface $translator;

    
    /**
     * Constructor.
     *
     * @param UserServiceInterface $userService User service
     * @param TranslatorInterface  $translator  Translator
     */
    public function __construct(UserServiceInterface $userService, TranslatorInterface $translator, UserPasswordHasherInterface $passwordHasher)
    {
        $this->userService = $userService;
        $this->translator = $translator;
        $this->passwordHasher = $passwordHasher;
    }

    /**
     * Index action.
     *
     * @param Request $request HTTP Request
     * @param Security $security logged user info
     *
     * @return Response HTTP response
     */
    #[Route(name: 'user_index', methods: 'GET')]
    #[IsGranted('VIEW', subject: 'user_list')]
    public function index(Request $request, Security $security): Response
    {
        if ($security->isGranted('ROLE_ADMIN')) {
            $pagination = $this->userService->getPaginatedList(
                $request->query->getInt('page', 1)
            );    
            return $this->render('user/index.html.twig', ['pagination' => $pagination]);
        } else {
            if ($security->isGranted('ROLE_USER')) {
                $pagination = $this->userService->getPaginatedListByAuthor(
                    $request->query->getInt('page', 1),
                    $this->getUser()
                );    
                return $this->render('user/index.html.twig', ['pagination' => $pagination]);
            } 
        }

        return $this->render('user/index.html.twig');
    }

    /**
     * Show action.
     *
     * @param User $user User entity
     * @param Security $security logged user info
     *
     * @return Response HTTP response
     */
    #[Route('/{id}', name: 'user_show', requirements: ['id' => '[1-9]\d*'], methods: 'GET', )]
    #[IsGranted('VIEW', subject: 'user')]
    public function show(User $user, Security $security): Response
    {
        if($this->getUser() == $user or $security->isGranted('ROLE_ADMIN'))
            return $this->render('user/show.html.twig', ['user' => $user]);
        else return $this->render('user/show.html.twig', ['user' => $this->getUser()]);
    }

    /**
     * Create action.
     *
     * @param Request $request HTTP request
     * @param Security $security logged user info
     *
     * @return Response HTTP response
     */
    #[Route('/create', name: 'user_create', methods: 'GET|POST')]
    #[IsGranted('ROLE_ADMIN')]
    public function create(Request $request, Security $security): Response
    {
        if(!$security->isGranted('ROLE_ADMIN')) return $this->redirectToRoute('home');
        /** @var User $user */
        $user = new User();
        $form = $this->createForm(
            UserType::class,
            $user,
            ['action' => $this->generateUrl('user_create')]
        );
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->userService->save($user);

            $this->addFlash(
                'success',
                $this->translator->trans('message.created_successfully')
            );

            return $this->redirectToRoute('user_index');
        }

        return $this->render(
            'user/create.html.twig',
            ['form' => $form->createView()]
        );
    }

    /**
     * Edit email action.
     *
     * @param Request $request HTTP request
     * @param User    $user    User entity
     * @param Security $security logged user info
     *
     * @return Response HTTP response
     */
    #[Route('/{id}/edit', name: 'user_edit', requirements: ['id' => '[1-9]\d*'], methods: 'GET|PUT')]
    public function edit(Request $request, User $user, Security $security): Response
    {

        if(!$security->isGranted('ROLE_ADMIN') and $this->getUser() != $user) return $this->redirectToRoute('user_index');

        $form = $this->createForm(
            UserType::class,
            $user,
            [
                'method' => 'PUT',
                'action' => $this->generateUrl('user_edit', ['id' => $user->getId()]),
            ]
        );

        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {

            // $user->setPassword(
            //     $this->passwordHasher->hashPassword(
            //         $user,
            //         $user->getPassword()
            //     )
            // );
            //dd($user);

            $this->userService->save($user);

            $this->addFlash(
                'success',
                $this->translator->trans('message.edited_successfully')
            );

            return $this->redirectToRoute('user_index');
        } else {
            $modifiedData = $form->getData();
            $modifiedData->setPassword('');
            $form->setData($modifiedData);
        }
        
        return $this->render(
            'user/edit.html.twig',
            [
                'form' => $form->createView(),
                'user' => $user,
            ]
        );
    }

     /**
     * Edit email action.
     *
     * @param Request $request HTTP request
     * @param User    $user    User entity
     * @param Security $security logged user info
     *
     * @return Response HTTP response
     */
    #[Route('/{id}/edit_password', name: 'user_edit_password', requirements: ['id' => '[1-9]\d*'], methods: 'GET|PUT')]
    public function edit_password(Request $request, User $user, Security $security): Response
    {
        if(!$security->isGranted('ROLE_ADMIN') and $this->getUser() != $user) return $this->redirectToRoute('user_index');
        $form = $this->createForm(
            PasswordType::class,
            $user,
            [
                'method' => 'PUT',
                'action' => $this->generateUrl('user_edit_password', ['id' => $user->getId()]),
            ]
        );

        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {

            $user->setPassword(
                $this->passwordHasher->hashPassword(
                    $user,
                    $user->getPassword()
                )
            );
            //dd($user);

            $this->userService->save($user);

            $this->addFlash(
                'success',
                $this->translator->trans('message.edited_successfully')
            );

            return $this->redirectToRoute('user_index');
        } else {
            $modifiedData = $form->getData();
            $modifiedData->setPassword('');
            $form->setData($modifiedData);
        }
        
        return $this->render(
            'user/edit_password.html.twig',
            [
                'form' => $form->createView(),
                'user' => $user,
            ]
        );
    }

    /**
     * Delete action.
     *
     * @param Request $request HTTP request
     * @param User    $user    User entity
     * @param Security $security logged user info
     *
     * @return Response HTTP response
     */
    #[Route('/{id}/delete', name: 'user_delete', requirements: ['id' => '[1-9]\d*'], methods: 'GET|DELETE')]
    public function delete(Request $request, User $user, Security $security): Response
    {
        if(!$security->isGranted('ROLE_ADMIN')) return $this->redirectToRoute('home');
        $form = $this->createForm(
            FormType::class,
            $user,
            [
                'method' => 'DELETE',
                'action' => $this->generateUrl('user_delete', ['id' => $user->getId()]),
            ]
        );
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->userService->delete($user);

            $this->addFlash(
                'success',
                $this->translator->trans('message.deleted_successfully')
            );

            return $this->redirectToRoute('user_index');
        }

        return $this->render(
            'user/delete.html.twig',
            [
                'form' => $form->createView(),
                'user' => $user,
            ]
        );
    }
}