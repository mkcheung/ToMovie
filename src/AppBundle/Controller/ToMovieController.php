<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use AppBundle\Entity\Movie;
use AppBundle\Service\ToMovieService;


class ToMovieController extends Controller
{

    public function indexAction(Request $request)
    {
         /** @var ToMovieService $toMovieService */
        $toMovieService = $this->get('app.to_movie_service');

        $movieTitles = $toMovieService->getAllOwnedMovieTitles();

		$form = $this->createFormBuilder()
            ->setAction($this->generateUrl('tomovies_show'))
            ->add('title', TextType::class, array('label' => 'Title'))
            ->add('save', SubmitType::class, array('label' => 'Get Movie(s)'))
            ->getForm();

        // replace this example code with whatever you need
        return $this->render('ToMovie/movie_input.html.twig', [
            'page_title' => 'Request A Movie',
            'form' => $form->createView(),
            'movieTitles' => $movieTitles
        ]);
    }

    public function showAction(Request $request)
    {
        $displayedMovies = [];

        $postParameters = $request->request->all();

        if(empty($postParameters['form']['title'])){

            $this->addFlash(
                'warning',
                'Please enter appropriate title.'
            );
            return $this->redirect($this->generateUrl('tomovies_index'));
        }

        $title = $postParameters['form']['title'];

         /** @var ToMovieService $toMovieService */
        $toMovieService = $this->get('app.to_movie_service');

        $response = $toMovieService->getMovies($title);

        $movies = $response->results;

        if(empty($movies)){

            $this->addFlash(
                'warning',
                'No movies with this title found.'
            );
            return $this->redirect($this->generateUrl('tomovies_index'));
        }

        $ownedMovieTitles = $toMovieService->getAllOwnedMovieTitles();

        $displayedMovies = $toMovieService->prepDisplayMovieFormat($movies, $ownedMovieTitles);

        return $this->render('ToMovie/movies.html.twig', [
            'page_title'   => 'Movies',
            'numMatches' => $response->total_results,
            'movies' => $displayedMovies
        ]);
    }

    public function saveAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $postParameters = $request->request->all();

        try {

            $movieTitles = (!empty($postParameters['movie_owned_list'])) ? $postParameters['movie_owned_list'] : [] ; ;
            $movieNotOwned = (!empty($postParameters['movie_not_owned_list'])) ? $postParameters['movie_not_owned_list'] : [] ;

            $movieNotOwnedResults = $em->getRepository(Movie::class)->findBy(['title' => $movieNotOwned]);

            foreach ($movieNotOwnedResults as $movieNotOwnedResult){
                $em->remove($movieNotOwnedResult);
            }
            $em->flush();

            $movieTitleResults = $em->getRepository(Movie::class)->findBy(['title' => $movieTitles]);
            foreach ($movieTitleResults as $movieTitleResult) {
                $title = $movieTitleResult->getTitle();
                if(in_array($title, $movieTitles)){
                    $key = array_search($title, $movieTitles);
                    unset($movieTitles[$key]);
                }
            }

            foreach ($movieTitles as $movieTitle){

                $movie = new Movie($movieTitle, 1);
                $em->persist($movie);
            }

            $em->flush();

            $this->addFlash(
                'success',
                'Owned Movies modified!'
            );
            
            return $this->redirect($this->generateUrl('tomovies_index'));
        } catch (\Exception $e){

            $this->addFlash(
                'warning',
                'Error modifying owned movies!'
            );
            return $this->redirect($this->generateUrl('tomovies_index'));

        }
    }
}
