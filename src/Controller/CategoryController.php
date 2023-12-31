<?php





/**
 * Category controller.
 */

 namespace App\Controller;

 use App\Service\QuestionService;
 use App\Repository\QuestionRepository;
 use App\Entity\Category;
 use App\Form\Type\CategoryType;
 use App\Service\CategoryServiceInterface;
 use App\Service\QuestionServiceInterface;
 use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
 use Symfony\Component\HttpFoundation\Request;
 use Symfony\Component\HttpFoundation\Response;
 use Symfony\Component\Routing\Annotation\Route;
 use Symfony\Contracts\Translation\TranslatorInterface;
 use Symfony\Component\Form\Extension\Core\Type\FormType;
 
 /**
  * Class CategoryController.
  */
 #[Route('/category')]
 class CategoryController extends AbstractController
 {
     /**
      * Category service.
      */
     private CategoryServiceInterface $categoryService;

     /**
      * Question service.
      */
      private QuestionServiceInterface $questionService;
 
     /**
      * Translator.
      *
      * @var TranslatorInterface
      */
     private TranslatorInterface $translator;
 
     /**
      * Constructor.
      *
      * @param CategoryServiceInterface $categoryService Category service
      * @param QuestionServiceInterface $questionService Question service
      * @param TranslatorInterface      $translator  Translator
      */
     public function __construct(CategoryServiceInterface $categoryService, QuestionServiceInterface $questionService, TranslatorInterface $translator)
     {
         $this->categoryService = $categoryService;
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
    #[Route(name: 'category_index', methods: 'GET')]
    public function index(Request $request): Response
    {
        $pagination = $this->categoryService->getPaginatedList(
            $request->query->getInt('page', 1)
        );

        return $this->render('category/index.html.twig', ['pagination' => $pagination]);
    }

    /**
     * Show action.
     *
     * @param Category $category Category
     * @param Request $request HTTP Request
     *
     * @return Response HTTP response
     */
    #[Route('/{id}', name: 'category_show', requirements: ['id' => '[1-9]\d*'], methods: 'GET' )]
    public function show(Category $category, Request $request): Response
    {   

          $pagination = $this->questionService->getPaginatedListByCategory(
            $request->query->getInt('page', 1),
            $category,
        );
        
        return $this->render('category/show.html.twig',[
            'pagination' => $pagination,
            'category' => $category
    ]);
    }

     /**
     * Create action.
     *
     * @param Request $request HTTP request
     *
     * @return Response HTTP response
     */
    #[Route( '/create', name: 'category_create', methods: 'GET|POST')]
    public function create(Request $request): Response
    {
        $category = new Category();
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->categoryService->save($category);

            $this->addFlash(
                'success',
                $this->translator->trans('message.created_successfully')
            );

            return $this->redirectToRoute('category_index');
        }

        return $this->render(
            'category/create.html.twig',
            ['form' => $form->createView()]
        );
    }

    /**
     * Edit action.
     *
     * @param Request  $request  HTTP request
     * @param Category $category Category entity
     *
     * @return Response HTTP response
     */
    #[Route('/{id}/edit', name: 'category_edit', requirements: ['id' => '[1-9]\d*'], methods: 'GET|PUT')]
    public function edit(Request $request, Category $category): Response
    {
       // dd($category);
        $form = $this->createForm(
            CategoryType::class,
            $category,
            [
                'method' => 'PUT',
                'action' => $this->generateUrl('category_edit', ['id' => $category->getId()]),
            ]
        );
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            //dd($category);
            $this->categoryService->save($category);

            $this->addFlash(
                'success',
                $this->translator->trans('message.created_successfully')
            );

            return $this->redirectToRoute('category_index');
        }

        return $this->render(
            'category/edit.html.twig',
            [
                'form' => $form->createView(),
                'category' => $category,
            ]
        );
    }

    /**
     * Delete action.
     *
     * @param Request  $request  HTTP request
     * @param Category $category Category entity
     *
     * @return Response HTTP response
     */
    #[Route('/{id}/delete', name: 'category_delete', requirements: ['id' => '[1-9]\d*'], methods: 'GET|DELETE')]
    public function delete(Request $request, Category $category): Response
    {
       // dd($category);
        if(!$this->categoryService->canBeDeleted($category)) {
            $this->addFlash(
                'warning',
                $this->translator->trans('message.category_contains_questions')
            );

            return $this->redirectToRoute('category_index');
        }

        $form = $this->createForm(
            FormType::class, 
            $category, 
            [
              'method' => 'DELETE',
              'action' => $this->generateUrl('category_delete', ['id' => $category->getId()]),
            ]
        );
        $form->handleRequest($request);

        
        if ($form->isSubmitted() && $form->isValid()) {
           
            $this->categoryService->delete($category);

            $this->addFlash(
                'success',
                $this->translator->trans('message.deleted_successfully')
            );

            return $this->redirectToRoute('category_index');
        }

        return $this->render(
            'category/delete.html.twig',
            [
                'form' => $form->createView(),
                'category' => $category,
            ]
        );
    }
}
