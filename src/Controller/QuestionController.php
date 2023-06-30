<?php
/**
 * Question controller.
 */

namespace App\Controller;

use App\Entity\Question;
use App\Entity\Answer;
use App\Form\Type\QuestionType;
use App\Form\Type\AnswerType;
use App\Service\AnswerService;
use App\Form\Type\AnswerNouserType;
use App\Service\QuestionServiceInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * Class QuestionController.
 */
#[Route('/question')]
class QuestionController extends AbstractController
{
    /**
     * Question service.
     */
    private QuestionServiceInterface $questionService;

    /**
     * Translator.
     */
    private TranslatorInterface $translator;

    /**
     * Constructor.
     *
     * @param QuestionServiceInterface $questionService Question service
     * @param TranslatorInterface  $translator  Translator
     */
    public function __construct(QuestionServiceInterface $questionService, TranslatorInterface $translator)
    {
        $this->questionService = $questionService;
        $this->translator = $translator;
    }

    /**
     * Index action.
     *
     * @param Request $request HTTP Request
     *
     * @return Response HTTP response
     */
    #[Route(name: 'question_index', methods: 'GET')]
    #[IsGranted('VIEW', subject: 'question')]
    public function index(Request $request): Response
    {
        // $pagination = $this->questionService->getPaginatedList(
        //     $request->query->getInt('page', 1),
        //     $this->getUser()
        // );

        $pagination = $this->questionService->getPaginatedList(
            $request->query->getInt('page', 1),
        );

        return $this->render('question/index.html.twig', ['pagination' => $pagination]);
    }

    /**
     * Show action.
     *
     * @param Question $question Question entity
     *
     * @return Response HTTP response
     */
    #[Route('/{id}', name: 'question_show', requirements: ['id' => '[1-9]\d*'], methods: 'GET', )]
    #[IsGranted('VIEW', subject: 'question')]
    public function show(Question $question, AnswerService $answerService): Response
    {
       // dd($answerService->findBy(['question' => $question]));

       //show answers in question with sorting by bestAnswer and date of create
        return $this->render('question/show.html.twig', 
            ['question' => $question, 
            'answers' => $answerService->findBy(['question' => $question],["bestAnswer"=>"DESC","createdAt"=>"DESC"]),
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
    #[Route('/create', name: 'question_create', methods: 'GET|POST')]
    public function create(Request $request): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        $question = new Question();
        $question->setAuthor($user);
        $form = $this->createForm(
            QuestionType::class,
            $question,
            ['action' => $this->generateUrl('question_create')]
        );
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->questionService->save($question);

            $this->addFlash(
                'success',
                $this->translator->trans('message.created_successfully')
            );

            return $this->redirectToRoute('question_index');
        }

        return $this->render(
            'question/create.html.twig',
            ['form' => $form->createView()]
        );
    }

    /**
     * Edit action.
     *
     * @param Request $request HTTP request
     * @param Question    $question    Question entity
     *
     * @return Response HTTP response
     */
    #[Route('/{id}/edit', name: 'question_edit', requirements: ['id' => '[1-9]\d*'], methods: 'GET|PUT')]
    #[IsGranted('EDIT', subject: 'question')]
    public function edit(Request $request, Question $question): Response
    {   

        $form = $this->createForm(
            QuestionType::class,
            $question,
            [
                'method' => 'PUT',
                'action' => $this->generateUrl('question_edit', ['id' => $question->getId()]),
            ]
        );
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->questionService->save($question);

            $this->addFlash(
                'success',
                $this->translator->trans('message.edited_successfully')
            );

            return $this->redirectToRoute('question_index');
        }

        return $this->render(
            'question/edit.html.twig',
            [
                'form' => $form->createView(),
                'question' => $question,
            ]
        );
    }

    /**
     * Delete action.
     *
     * @param Request $request HTTP request
     * @param Question    $question    Question entity
     *
     * @return Response HTTP response
     */
    #[Route('/{id}/delete', name: 'question_delete', requirements: ['id' => '[1-9]\d*'], methods: 'GET|DELETE')]
    #[IsGranted('DELETE', subject: 'question')]
    public function delete(Request $request, Question $question): Response
    {
        $form = $this->createForm(
            FormType::class,
            $question,
            [
                'method' => 'DELETE',
                'action' => $this->generateUrl('question_delete', ['id' => $question->getId()]),
            ]
        );
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->questionService->delete($question);

            $this->addFlash(
                'success',
                $this->translator->trans('message.deleted_successfully')
            );

            return $this->redirectToRoute('question_index');
        }

        return $this->render(
            'question/delete.html.twig',
            [
                'form' => $form->createView(),
                'question' => $question,
            ]
        );
    }


     /**
     * Answer action.
     *
     * @param \Symfony\Component\HttpFoundation\Request $request        HTTP request
     * @param \App\Entity\Question                          $question           Question entity
     * @param AnswerService                            $answerService
     *
     * @return \Symfony\Component\HttpFoundation\Response HTTP response
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     *
     */
    #[Route('/{id}/answer', name: 'question_answer', requirements: ['id' => '[1-9]\d*'], methods: 'GET|POST')]
    #[IsGranted('ROLE_USER')]
    public function answer(Request $request, Question $question, AnswerService $answerService): Response
    {
        $answer = new Answer();
        if($this->getUser()!=null) {
            $form = $this->createForm(AnswerType::class, $answer);
        } else {
            $form = $this->createForm(AnswerNouserType::class, $answer);
        }
        $form->handleRequest($request);
        
        if($this->getUser()!=null) {
            if ($form->isSubmitted() && $form->isValid()) {
                $answer->setQuestion($question);
                $answer->setAuthor($this->getUser());
                $answer->setCreatedAt(new \DateTime());
                $answer->setUpdatedAt(new \DateTime());
                $answerService->save($answer);
                $this->addFlash('success', 'message_added_successfully');
                return $this->redirectToRoute('question_show', ['id' => $question->getId()]);
            }
        } else {
            if ($form->isSubmitted() && $form->isValid()) {
                $answer->setQuestion($question);
                $answer->setAuthor(null);
                $answer->setCreatedAt(new \DateTime());
                $answer->setUpdatedAt(new \DateTime());
                $answerService->save($answer);
                $this->addFlash('success', 'message_added_successfully');
                return $this->redirectToRoute('question_show', ['id' => $question->getId()]);
    
            }
        }

        return $this->render(
            'question/answer.html.twig',
            [
                'form' => $form->createView(),
                'question' => $question,
                'answer' => $answer,
            ]
        );
    }
}