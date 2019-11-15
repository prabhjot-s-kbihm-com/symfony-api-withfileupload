<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use App\Entity\Movie;
use App\Form\MovieType;
use App\Service\FileUploader;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
/**
 * Movie controller.
 * @Route("/api", name="api_")
 */
class MovieController extends FOSRestController
{
  /**
   * Lists all Movies.
   * @Rest\Get("/movies")
   *
   * @return Response
   */
  public function getMovieAction()
  {
    $repository = $this->getDoctrine()->getRepository(Movie::class);
    $movies = $repository->findall();
    return $this->handleView($this->view($movies));
  }

  /**
   * Create Movie.
   * @Rest\Post("/movie")
   *
   * @return Response
   */
  public function postMovieAction(Request $request, FileUploader $fileUploader)
  {
    
    $name = $request->get('name');
    if (empty($name)) {
      throw new BadRequestHttpException('"Name" is required');
    }

    $uploadedFile = $request->files->get('file');
    if (!$uploadedFile) {
        throw new BadRequestHttpException('"file" is required');
    }
    $movie = new Movie();
    $movie->setDescription('File Not Found');
    if ($uploadedFile) {
      $brochureFileName = $fileUploader->upload($uploadedFile);
      $movie->setDescription($brochureFileName);
    }

    $movie->setName($name);
    $movie->setDescription($brochureFileName);

      $em = $this->getDoctrine()->getManager();
      $em->persist($movie);
      $em->flush();
      return $this->handleView($this->view(['status' => 'ok'], Response::HTTP_CREATED));

  }

}
