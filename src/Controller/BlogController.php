<?php

namespace App\Controller;

use App\Repository\BlogRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Attribute\Route;

final class BlogController extends AbstractController
{
    public function __construct(
        private BlogRepository $blogRepository,
    ) {
    }

    #[Route('/blog', name: 'app_blog')]
    public function index(Request $request): Response
    {
        $blogsPerPage = 6;
        $page = (int) $request->query->get('page', 1);

        if (filter_var($page, FILTER_VALIDATE_INT) === false) {
            $page = 1;
        }

        $page = 0 >= $page ? 1 : $page;
        $offset = ($page - 1) * $blogsPerPage;

        $blogs = $this->blogRepository->findWithOffsetAndLimit($offset, $blogsPerPage);

        return $this->render('blog/index.html.twig', [
            'blogs' => $blogs,
            'pages' => ceil($this->blogRepository->count() / $blogsPerPage),
            'page' => $page,
        ]);
    }

    #[Route('/blog/{slug}', name: 'app_view_single_blog')]
    public function articleViewSingle(string $slug): Response
    {
        $blog = $this->blogRepository->findOneBy(['slug' => $slug]);

        if ($blog === null) {
            throw new NotFoundHttpException();
        }

        $suggestedBlogs = $this->blogRepository->getSuggestedBlogs($blog);

        return $this->render('blog/view_single.html.twig', [
            'blog' => $blog,
            'suggested_blogs' => $suggestedBlogs
        ]);
    }
}
