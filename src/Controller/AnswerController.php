<?php
/**
 * Answer controller.
 */

namespace App\Controller;

use App\Entity\Answer;
use App\Service\AnswerService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class AnswerController.
 *
 * @Route("/answer")
 *
 * @IsGranted("ROLE_USER")
 */
class AnswerController extends AbstractController
{
    /**
     * Answer service.
     *
     * @var AnswerService
     */
    private AnswerService $answerService;

    /**
     * AnswerController constructor.
     *
     * @param AnswerService $answerService Answer service
     */
    public function __construct(AnswerService $answerService)
    {
        $this->answerService = $answerService;
    }

    /**
     * Delete action.
     *
     * @param \Symfony\Component\HttpFoundation\Request $request HTTP request
     * @param \App\Entity\Answer                       $answer Answer entity
     *
     * @return \Symfony\Component\HttpFoundation\Response HTTP response
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     *
     * @Route(
     *     "/{id}/delete",
     *     methods={"GET", "DELETE"},
     *     requirements={"id": "[1-9]\d*"},
     *     name="answer_delete",
     * )
     */
    public function deleteAnswer(Request $request, Answer $answer): Response
    {
        $form = $this->createForm(FormType::class, $answer, ['method' => 'DELETE']);
        $form->handleRequest($request);

        if ($request->isMethod('DELETE') && !$form->isSubmitted()) {
            $form->submit($request->request->get($form->getName()));
        }

        if ($form->isSubmitted() && $form->isValid()) {
            $this->answerService->delete($answer);
            $this->addFlash('success', 'message_deleted_successfully');

            return $this->redirectToRoute('question_index');
        }

        return $this->render(
            'answer/delete.html.twig',
            [
                'form' => $form->createView(),
                'answer' => $answer,
            ]
        );
    }
  

    /**
     * Best answer action.
     *
     * @param \Symfony\Component\HttpFoundation\Request $request HTTP request
     * @param \App\Entity\Answer                       $answer Answer entity
     *
     * @return \Symfony\Component\HttpFoundation\Response HTTP response
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     *
     * @Route(
     *     "/{id}/answer_best_answer",
     *     methods={"GET", "POST"},
     *     requirements={"id": "[1-9]\d*"},
     *     name="answer_best_answer",
     * )
     */
    public function bestAnswer(Request $request, Answer $answer): Response
    {
        $form = $this->createForm(FormType::class, $answer, ['method' => 'POST']);
        $form->handleRequest($request);

        // if ($request->isMethod('POST') && !$form->isSubmitted()) {
        //     $form->submit($request->request->get($form->getName()));
        // }

        if ($form->isSubmitted() && $form->isValid()) {
            $this->answerService->saveBestAnswer($answer);
            $this->addFlash('success', 'message.best_answer_chosen');

            return $this->redirectToRoute('question_show', ['id' => $answer->getQuestion()->getId()]);
            //return $this->redirectToRoute('question_index');
        }

        return $this->render(
            'answer/bestanswer.html.twig',
            [
                'form' => $form->createView(),
                'answer' => $answer,
            ]
        );
    }

}