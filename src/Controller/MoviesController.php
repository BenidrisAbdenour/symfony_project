<?php

namespace App\Controller;

use App\Entity\Movie;
use App\Form\MovieFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;



#[Route('/movies', name: 'movies.')]
class MoviesController extends AbstractController
{

    private $em;
    private $movieRepository;

    public function __construct(EntityManagerInterface $entityManger)
    {
        $this->em = $entityManger;
        $this->movieRepository = $this->em->getRepository(Movie::class);
    }

    #[Route('/', name: 'list')]
    public function index(): Response
    {
        $movies = $this->movieRepository->findAll();
        return $this->render('movies/index.html.twig', [
            "movies" => $movies
        ]);
    }


    #[Route('/show{{id}}', name: 'show')]
    public function show($id): Response
    {
        $movie = $this->movieRepository->find($id);
        return $this->render('movies/show.html.twig', [
            "movie" => $movie
        ]);
    }

    #[Route('/create', name: 'create')]
    public function create(Request $request): Response
    {

        $movie = new Movie();
        $form = $this->createForm(MovieFormType::class, $movie);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $newMovie = $form->getData();
            $file = $form->get('imagePath')->getData();
            if ($file) {
                $newFileName = uniqid() . '.' . $file->guessExtension();

                try {
                    $file->move(
                        $this->getParameter('kernel.project_dir') . '/public/uploads',
                        $newFileName
                    );
                } catch (FileException $e) {
                    return new Response($e->getMessage());
                }

                $newMovie->setImagePath('uploads/' . $newFileName);
                $this->em->persist($newMovie);
                $this->em->flush();
                return $this->redirectToRoute('movies.list');
            } else {
                $this->addFlash('Error', 'You have to upload an a image !');
                return $this->redirectToRoute('movies.create');
            }
        }


        return $this->render('movies/create.html.twig', [
            'form' => $form->createView()
        ]);
    }



    #[Route('/update{{id}}', name: 'update')]
    public function update($id, Request $request): Response
    {

        $movie = $this->movieRepository->find($id);
        $form = $this->createForm(MovieFormType::class, $movie);

        $form->handleRequest($request);
        $imagePath = $form->get('imagePath')->getData();

        if ($form->isSubmitted() && $form->isValid()) {
            if ($imagePath) {
                if ($movie->getImagePath() !== null) {
                    $old_image = $this->getParameter('kernel.project_dir') . '/public/' . $movie->getImagePath();
                    if (file_exists($old_image)) {
                        unlink($old_image);
                    }
                    do {
                        $newFileName = uniqid() . '.' . $imagePath->guessExtension();
                        $new_image = $this->getParameter('kernel.project_dir') . '/public/uploads/' . $newFileName;
                    } while (file_exists($new_image));

                    try {
                        $imagePath->move(
                            $this->getParameter('kernel.project_dir') . '/public/uploads',
                            $newFileName
                        );
                    } catch (FileException $e) {
                        return new Response($e->getMessage());
                    }

                    $movie->setImagePath('uploads/' . $newFileName);
                    $this->em->flush();
                    $this->addFlash('success', 'Movie post has been updated successfully');
                    return $this->redirectToRoute('movies.show', ["id" => $id]);
                }
            } else {
                $this->em->flush();
                $this->addFlash('success', 'Movie post has been updated successfully');
                return $this->redirectToRoute('movies.show', ["id" => $id]);
            }
        }




        return $this->render('movies/edit.html.twig', [
            "movie" => $movie,
            "form" => $form->createView()
        ]);
    }


    #[Route('/delete{{id}}', name: 'delete')]
    public function delete($id): Response
    {
        $movie = $this->movieRepository->find($id);
        $old_image = $this->getParameter('kernel.project_dir') . '/public/' . $movie->getImagePath();
        if (file_exists($old_image)) {
            unlink($old_image);
        }
        $this->em->remove($movie);
        $this->em->flush();

        $this->addFlash('success', 'Movie post has been deleted successfully');
        return $this->redirectToRoute('movies.list');
    }
}
