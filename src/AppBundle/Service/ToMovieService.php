<?php
namespace AppBundle\Service;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use JsonRPC\Client as JsonRPCClient;
use JsonRPC\MiddlewareInterface;
use JsonRPC\Exception\AuthenticationFailureException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use GuzzleHttp\Client as GuzzleClient;
class ToMovieService
{
    private $api_key;

    public function __construct(
        EntityManager $entityManager,
        EntityRepository $movieRepository,
        $toMovieApiKey
    ) {
        $this->em = $entityManager;
        $this->movieRepository = $movieRepository;
        $this->api_key = $toMovieApiKey;
    }
    
    public function getMovies($title)
    {        

        $movieApiUrl = 'https://api.themoviedb.org/3/search/movie?include_adult=false&page=1&language=en-US&api_key='.$this->api_key.'&query=\''.$title.'\'';

        $client       = new GuzzleClient();
        $res          = $client->request('GET', $movieApiUrl);
        $jsonResponse = $res->getBody();
        $response    = json_decode($jsonResponse);

        return $response;
    }

    public function getAllOwnedMovieTitles($unique=false){

        $movieTitles = [];
        $movies = $this->movieRepository->findAll();
        if($unique) {
            foreach ($movies as $movie) {
                array_push($movieTitles,$movie->getUniqueTitle());
            }
        } else {
            foreach ($movies as $movie) {
                array_push($movieTitles,$movie->getTitle());
            }
        }
        return $movieTitles;
    }

    public function prepDisplayMovieFormat($movies, $ownedUniqueMovieTitles){

        $displayedMovies = [];

        $movieCount = count($movies);

        if($movieCount > 0){

            $numMoviesToDisplay = ($movieCount < 10) ? $movieCount : 10;

            for($i = 0; $i < $numMoviesToDisplay ; $i++){
                $displayedMovies[$i]['id'] = strtolower(preg_replace('/[^A-Za-z0-9\-]/', '', preg_replace('/\s+/', '_', $movies[$i]->title)));
                $displayedMovies[$i]['title'] = $movies[$i]->title;
                $displayedMovies[$i]['unique_title'] = $movies[$i]->title.'_'.$movies[$i]->release_date;
                $displayedMovies[$i]['release_date'] = $movies[$i]->release_date;
                $displayedMovies[$i]['overview'] = $movies[$i]->overview;
                $displayedMovies[$i]['owned'] = false;
            }
        }

        foreach ($displayedMovies as &$displayedMovie) {
            if(in_array($displayedMovie['unique_title'], $ownedUniqueMovieTitles)){
                $displayedMovie['owned'] = true;
            }
        }

        return $displayedMovies;
    }
    
}