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

    public function setUp()
    {
        self::bootKernel();
        $this->truncateEntities([
            Movie::class,
        ]);
    }

    public function testGetMovies(){

        $expected = file_get_contents(__DIR__ . '/../mockedToMovieResponse.txt');
        $mock = new MockHandler([new Response(200, [], $expected)]);
        $handler = HandlerStack::create($mock);
        $client = new Client(['handler' => $handler]);

        $toMovieService = new ToMovieService(
            $this->getEntityManager(),
            $this->getEntityRepository(),
            $client,
            'testing123'
        );
        $result = $toMovieService->getMovies('Star Wars');
        $resultArray = (array)$result;
        $this->assertArrayHasKey('results', $resultArray);
        $this->assertCount(3, $resultArray['results']);
        $this->assertEquals("Star Trek Into Darkness", $resultArray['results'][0]->title);
        $this->assertEquals("Star Trek II: The Wrath of Khan", $resultArray['results'][1]->title);
        $this->assertEquals("Star Trek 25th Anniversary Special", $resultArray['results'][2]->title);

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