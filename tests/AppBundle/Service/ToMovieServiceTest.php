<?php
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

/**
 * Created by PhpStorm.
 * User: marscheung
 * Date: 1/19/19
 * Time: 12:00 PM
 */

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
        $dinoFactory = $this->createMock(DinosaurFactory::class);
        $dinoFactory->expects($this->any())
            ->method('growFromSpecification')
            ->willReturnCallback(function($spec) {
                return new Dinosaur();
            });
        $toMovieService = new ToMovieService(
            $this->getEntityManager(),
            $this->getEntityRepository(),
            'testing123'
        );
        $mock = new MockHandler([new Response($status, [], $body)]);
        $handler = HandlerStack::create($mock);
        $client = new Client(['handler' => $handler]);
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
            ->getRepository();
    }
}