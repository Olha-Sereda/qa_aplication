<?php
/**
 * Task controller.
 */

namespace App\Controller;

use App\Entity\Task;
use App\Entity\Comment;
use App\Form\Type\TaskType;
use App\Form\Type\CommentType;
use App\Service\CommentService;
use App\Form\Type\CommentNouserType;
use App\Service\TaskServiceInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * Class TaskController.
 */
#[Route('/task')]
class TaskController extends AbstractController
{
    /**
     * Task service.
     */
    private TaskServiceInterface $taskService;

    /**
     * Translator.
     */
    private TranslatorInterface $translator;

    /**
     * Constructor.
     *
     * @param TaskServiceInterface $taskService Task service
     * @param TranslatorInterface  $translator  Translator
     */
    public function __construct(TaskServiceInterface $taskService, TranslatorInterface $translator)
    {
        $this->taskService = $taskService;
        $this->translator = $translator;
    }

    /**
     * Index action.
     *
     * @param Request $request HTTP Request
     *
     * @return Response HTTP response
     */
    #[Route(name: 'task_index', methods: 'GET')]
    #[IsGranted('VIEW', subject: 'task')]
    public function index(Request $request): Response
    {
        // $pagination = $this->taskService->getPaginatedList(
        //     $request->query->getInt('page', 1),
        //     $this->getUser()
        // );

        $pagination = $this->taskService->getPaginatedList(
            $request->query->getInt('page', 1),
        );

        return $this->render('task/index.html.twig', ['pagination' => $pagination]);
    }

    /**
     * Show action.
     *
     * @param Task $task Task entity
     *
     * @return Response HTTP response
     */
    #[Route('/{id}', name: 'task_show', requirements: ['id' => '[1-9]\d*'], methods: 'GET', )]
    #[IsGranted('VIEW', subject: 'task')]
    public function show(Task $task, CommentService $commentService): Response
    {
       // dd($commentService->findBy(['task' => $task]));

       //show comments in task with sorting by bestAnswer and date of create
        return $this->render('task/show.html.twig', 
            ['task' => $task, 
            'comments' => $commentService->findBy(['task' => $task],["bestAnswer"=>"DESC","createdAt"=>"DESC"]),
            'userid' => $this->getUser()
        ]);
    }

    /**
     * Create action.
     *
     * @param Request $request HTTP request
     *
     * @return Response HTTP response
     */
    #[Route('/create', name: 'task_create', methods: 'GET|POST')]
    public function create(Request $request): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        $task = new Task();
        $task->setAuthor($user);
        $form = $this->createForm(
            TaskType::class,
            $task,
            ['action' => $this->generateUrl('task_create')]
        );
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->taskService->save($task);

            $this->addFlash(
                'success',
                $this->translator->trans('message.created_successfully')
            );

            return $this->redirectToRoute('task_index');
        }

        return $this->render(
            'task/create.html.twig',
            ['form' => $form->createView()]
        );
    }

    /**
     * Edit action.
     *
     * @param Request $request HTTP request
     * @param Task    $task    Task entity
     *
     * @return Response HTTP response
     */
    #[Route('/{id}/edit', name: 'task_edit', requirements: ['id' => '[1-9]\d*'], methods: 'GET|PUT')]
    #[IsGranted('EDIT', subject: 'task')]
    public function edit(Request $request, Task $task): Response
    {   

        $form = $this->createForm(
            TaskType::class,
            $task,
            [
                'method' => 'PUT',
                'action' => $this->generateUrl('task_edit', ['id' => $task->getId()]),
            ]
        );
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->taskService->save($task);

            $this->addFlash(
                'success',
                $this->translator->trans('message.edited_successfully')
            );

            return $this->redirectToRoute('task_index');
        }

        return $this->render(
            'task/edit.html.twig',
            [
                'form' => $form->createView(),
                'task' => $task,
            ]
        );
    }

    /**
     * Delete action.
     *
     * @param Request $request HTTP request
     * @param Task    $task    Task entity
     *
     * @return Response HTTP response
     */
    #[Route('/{id}/delete', name: 'task_delete', requirements: ['id' => '[1-9]\d*'], methods: 'GET|DELETE')]
    #[IsGranted('DELETE', subject: 'task')]
    public function delete(Request $request, Task $task): Response
    {
        $form = $this->createForm(
            FormType::class,
            $task,
            [
                'method' => 'DELETE',
                'action' => $this->generateUrl('task_delete', ['id' => $task->getId()]),
            ]
        );
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->taskService->delete($task);

            $this->addFlash(
                'success',
                $this->translator->trans('message.deleted_successfully')
            );

            return $this->redirectToRoute('task_index');
        }

        return $this->render(
            'task/delete.html.twig',
            [
                'form' => $form->createView(),
                'task' => $task,
            ]
        );
    }


     /**
     * Comment action.
     *
     * @param \Symfony\Component\HttpFoundation\Request $request        HTTP request
     * @param \App\Entity\Task                          $task           Task entity
     * @param CommentService                            $commentService
     *
     * @return \Symfony\Component\HttpFoundation\Response HTTP response
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     *
     */
    #[Route('/{id}/comment', name: 'task_comment', requirements: ['id' => '[1-9]\d*'], methods: 'GET|POST')]
    #[IsGranted('ROLE_USER')]
    public function comment(Request $request, Task $task, CommentService $commentService): Response
    {
        $comment = new Comment();
        if($this->getUser()!=null) {
            $form = $this->createForm(CommentType::class, $comment);
        } else {
            $form = $this->createForm(CommentNouserType::class, $comment);
        }
        $form->handleRequest($request);
        
        if($this->getUser()!=null) {
            if ($form->isSubmitted() && $form->isValid()) {
                $comment->setTask($task);
                $comment->setAuthor($this->getUser());
                $comment->setCreatedAt(new \DateTime());
                $comment->setUpdatedAt(new \DateTime());
                $commentService->save($comment);
                $this->addFlash('success', 'message_added_successfully');
                return $this->redirectToRoute('task_show', ['id' => $task->getId()]);
            }
        } else {
            if ($form->isSubmitted() && $form->isValid()) {
                $comment->setTask($task);
                $comment->setAuthor(null);
                $comment->setCreatedAt(new \DateTime());
                $comment->setUpdatedAt(new \DateTime());
                $commentService->save($comment);
                $this->addFlash('success', 'message_added_successfully');
                return $this->redirectToRoute('task_show', ['id' => $task->getId()]);
    
            }
        }

        return $this->render(
            'task/comment.html.twig',
            [
                'form' => $form->createView(),
                'task' => $task,
                'comment' => $comment,
            ]
        );
    }
}