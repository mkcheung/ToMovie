<?php
/**
 * Created by PhpStorm.
 * User: marscheung
 * Date: 1/19/19
 * Time: 12:00 PM
 */
namespace Tests\AppBundle\Service;
use AppBundle\Entity\Movie;
use AppBundle\Service\ToMovieService;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use function var_dump;


class ToMovieServiceTest extends KernelTestCase
{

    protected $client;
    protected $toMovieService;
    protected $movies;

    public function setUp()
    {
        self::bootKernel();
        $this->truncateEntities([
            Movie::class,
        ]);
        $expected = file_get_contents(__DIR__ . '/../mockedToMovieResponse.txt');
        $mock = new MockHandler([new Response(200, [], $expected)]);
        $handler = HandlerStack::create($mock);
        $this->client = new Client(['handler' => $handler]);

        $this->toMovieService = new ToMovieService(
            $this->getEntityManager(),
            $this->getEntityRepository(),
            $this->client,
            'testing123'
        );

        $this->movies = [
            $this->createMovie('Star Trek Into Darkness', 1, 'Star Trek Into Darkness_2013-05-05'),
            $this->createMovie('Star Trek II: The Wrath of Khan', 2, 'Star Trek II: The Wrath of Khan_1982-06-03'),
            $this->createMovie('Star Trek 25th Anniversary Special', 3, 'Star Trek 25th Anniversary Special_1991-09-28'),
        ];
    }

    public function testGetMovies(){


        $result = $this->toMovieService->getMovies('Star Wars');
        $resultArray = (array)$result;
        $this->assertArrayHasKey('results', $resultArray);
        $this->assertCount(3, $resultArray['results']);
        $this->assertEquals("Star Trek Into Darkness", $resultArray['results'][0]->title);
        $this->assertEquals("Star Trek II: The Wrath of Khan", $resultArray['results'][1]->title);
        $this->assertEquals("Star Trek 25th Anniversary Special", $resultArray['results'][2]->title);

    }

    public function testGetAllOwnedMovieTitlesNonUnique(){

        $mockEm = \Mockery::mock(EntityManager::class);

        $mockRepo = \Mockery::mock(EntityRepository::class);
        $mockRepo->shouldReceive('findAll')->andReturn($this->movies);

        $mockClient = \Mockery::mock(Client::class);


        $toMovieService = new ToMovieService(
            $mockEm,
            $mockRepo,
            $mockClient,
            'testing123'
        );

        $movieTitles = $toMovieService->getAllOwnedMovieTitles();
        $this->assertIsArray($movieTitles);
        $this->assertCount(3, $movieTitles);
        $this->assertEquals("Star Trek Into Darkness", $movieTitles[0]);
        $this->assertEquals("Star Trek II: The Wrath of Khan", $movieTitles[1]);
        $this->assertEquals("Star Trek 25th Anniversary Special", $movieTitles[2]);
    }

    public function testGetAllOwnedMovieTitlesUnique(){

        $mockEm = \Mockery::mock(EntityManager::class);

        $mockRepo = \Mockery::mock(EntityRepository::class);
        $mockRepo->shouldReceive('findAll')->andReturn($this->movies);

        $mockClient = \Mockery::mock(Client::class);

        $toMovieService = new ToMovieService(
            $mockEm,
            $mockRepo,
            $mockClient,
            'testing123'
        );

        $movieTitles = $toMovieService->getAllOwnedMovieTitles(true);
        $this->assertIsArray($movieTitles);
        $this->assertCount(3, $movieTitles);
        $this->assertEquals("Star Trek Into Darkness_2013-05-05", $movieTitles[0]);
        $this->assertEquals("Star Trek II: The Wrath of Khan_1982-06-03", $movieTitles[1]);
        $this->assertEquals("Star Trek 25th Anniversary Special_1991-09-28", $movieTitles[2]);
    }

    public function testPrepDisplayMovieFormat(){

        $mockEm = \Mockery::mock(EntityManager::class);

        $mockRepo = \Mockery::mock(EntityRepository::class);
        $mockRepo->shouldReceive('findAll')->andReturn($this->movies);

        $mockClient = \Mockery::mock(Client::class);

        $toMovieService = new ToMovieService(
            $mockEm,
            $mockRepo,
            $mockClient,
            'testing123'
        );

        $movies = file_get_contents(__DIR__ . '/../mockedToMovieResponse.txt');
        $movies = json_decode($movies);
        $displayedMovies = $toMovieService->prepDisplayMovieFormat($movies->results, []);
        $this->assertCount(3, $displayedMovies);

        $this->assertEquals("startrekintodarkness", $displayedMovies[0]['id']);
        $this->assertEquals("Star Trek Into Darkness", $displayedMovies[0]['title']);
        $this->assertEquals("Star Trek Into Darkness_2013-05-05", $displayedMovies[0]['unique_title']);
        $this->assertEquals("2013-05-05", $displayedMovies[0]['release_date']);
        $this->assertTrue(is_string($displayedMovies[0]['overview']));
        $this->assertFalse($displayedMovies[0]['owned']);

        $this->assertEquals("startrekiithewrathofkhan", $displayedMovies[1]['id']);
        $this->assertEquals("Star Trek II: The Wrath of Khan", $displayedMovies[1]['title']);
        $this->assertEquals("Star Trek II: The Wrath of Khan_1982-06-03", $displayedMovies[1]['unique_title']);
        $this->assertEquals("1982-06-03", $displayedMovies[1]['release_date']);
        $this->assertTrue(is_string($displayedMovies[1]['overview']));
        $this->assertFalse($displayedMovies[1]['owned']);

        $this->assertEquals("startrek25thanniversaryspecial", $displayedMovies[2]['id']);
        $this->assertEquals("Star Trek 25th Anniversary Special", $displayedMovies[2]['title']);
        $this->assertEquals("Star Trek 25th Anniversary Special_1991-09-28", $displayedMovies[2]['unique_title']);
        $this->assertEquals("1991-09-28", $displayedMovies[2]['release_date']);
        $this->assertTrue(is_string($displayedMovies[2]['overview']));
        $this->assertFalse($displayedMovies[2]['owned']);
    }
    public function testPrepDisplayMovieFormatOwnedEqualsTrue(){

        $mockEm = \Mockery::mock(EntityManager::class);

        $mockRepo = \Mockery::mock(EntityRepository::class);
        $mockRepo->shouldReceive('findAll')->andReturn($this->movies);

        $mockClient = \Mockery::mock(Client::class);

        $toMovieService = new ToMovieService(
            $mockEm,
            $mockRepo,
            $mockClient,
            'testing123'
        );

        $ownedMovies = [
            'Star Trek Into Darkness_2013-05-05'
        ];

        $movies = file_get_contents(__DIR__ . '/../mockedToMovieResponse.txt');
        $movies = json_decode($movies);
        $displayedMovies = $toMovieService->prepDisplayMovieFormat($movies->results, $ownedMovies);
        $this->assertCount(3, $displayedMovies);

        $this->assertEquals("startrekintodarkness", $displayedMovies[0]['id']);
        $this->assertEquals("Star Trek Into Darkness", $displayedMovies[0]['title']);
        $this->assertEquals("Star Trek Into Darkness_2013-05-05", $displayedMovies[0]['unique_title']);
        $this->assertEquals("2013-05-05", $displayedMovies[0]['release_date']);
        $this->assertTrue(is_string($displayedMovies[0]['overview']));
        $this->assertTrue($displayedMovies[0]['owned']);

        $this->assertEquals("startrekiithewrathofkhan", $displayedMovies[1]['id']);
        $this->assertEquals("Star Trek II: The Wrath of Khan", $displayedMovies[1]['title']);
        $this->assertEquals("Star Trek II: The Wrath of Khan_1982-06-03", $displayedMovies[1]['unique_title']);
        $this->assertEquals("1982-06-03", $displayedMovies[1]['release_date']);
        $this->assertTrue(is_string($displayedMovies[1]['overview']));
        $this->assertFalse($displayedMovies[1]['owned']);

        $this->assertEquals("startrek25thanniversaryspecial", $displayedMovies[2]['id']);
        $this->assertEquals("Star Trek 25th Anniversary Special", $displayedMovies[2]['title']);
        $this->assertEquals("Star Trek 25th Anniversary Special_1991-09-28", $displayedMovies[2]['unique_title']);
        $this->assertEquals("1991-09-28", $displayedMovies[2]['release_date']);
        $this->assertTrue(is_string($displayedMovies[2]['overview']));
        $this->assertFalse($displayedMovies[2]['owned']);
    }

    private function createMovie($title, $userId, $uniqueTitle)
    {
        $movie = new Movie($title, $userId, $uniqueTitle);

        return $movie;
    }


    private function truncateEntities(array $entities)
    {
        $purger = new ORMPurger($this->getEntityManager());
        $purger->purge();
    }

    /**
     * @return EntityManager
     */
    private function getEntityManager()
    {
        return self::$kernel->getContainer()
            ->get('doctrine')
            ->getManager();
    }

    /**
     * @return EntityRepository
     */
    private function getEntityRepository()
    {
        return self::$kernel->getContainer()
            ->get('doctrine')
            ->getRepository(Movie::class);
    }
}