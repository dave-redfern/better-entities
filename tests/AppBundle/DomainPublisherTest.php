<?php

namespace AppBundle\Tests;

use AppBundle\Entities\Comment;
use AppBundle\Entities\Post;
use AppBundle\Support\Doctrine\Subscribers\DomainEventPublisher;
use AppBundle\Support\Doctrine\Types\DateTimeType;
use AppBundle\Tests\Stubs\DomainEventListener;
use AppBundle\ValueObjects\EmailAddress;
use AppBundle\ValueObjects\PostAuthor;
use AppBundle\ValueObjects\PostContent;
use AppBundle\ValueObjects\PostTitle;
use Doctrine\Common\Cache\ArrayCache;
use Doctrine\Common\EventManager;
use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\Configuration;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\Driver\YamlDriver;
use Doctrine\ORM\Tools\SchemaTool;
use PHPUnit\Framework\TestCase;

/**
 * Class DomainPublisherTest
 *
 * @package    AppBundle\Tests
 * @subpackage AppBundle\Tests\DomainPublisherTest
 */
class DomainPublisherTest extends TestCase
{

    /**
     * @group database
     * @group domain
     * @group entities
     * @group entities-post
     */
    public function testCreateBroadcastsDomainEvents()
    {
        $entity = Post::create(
            $pa = new PostAuthor('bob', new EmailAddress('bob@example.com')),
            $pt = new PostTitle('Test Post'),
            $pc = new PostContent('<p>This is a test post</p>')
        );

        $this->em->persist($entity);

        $this->expectOutputString("Post titled Test Post was created and assigned id 1\n");

        $this->em->flush();

        $this->assertCount(0, $entity->releaseAndResetEvents());
    }

    /**
     * @group database
     * @group domain
     * @group entities
     * @group entities-post
     */
    public function testPublishBroadcastsDomainEvents()
    {
        $entity = Post::createAndPublish(
            $pa = new PostAuthor('bob', new EmailAddress('bob@example.com')),
            $pt = new PostTitle('Test Post'),
            $pc = new PostContent('<p>This is a test post</p>')
        );

        $this->em->persist($entity);

        $this->expectOutputString("Post titled Test Post was created and assigned id 1\nPost titled Test Post was published with slug test-post\n");

        $this->em->flush();

        $this->assertCount(0, $entity->releaseAndResetEvents());
    }

    /**
     * @group database
     * @group domain
     * @group entities
     * @group entities-post
     */
    public function testUpdatesBroadcastsDomainEvents()
    {
        $entity = Post::createAndPublish(
            $pa = new PostAuthor('bob', new EmailAddress('bob@example.com')),
            $pt = new PostTitle('Test Post'),
            $pc = new PostContent('<p>This is a test post</p>')
        );
        $entity->releaseAndResetEvents();

        $this->em->flush();

        $entity->changeTitle(new PostTitle('I changed the title'));
        $this->em->persist($entity);

        $this->expectOutputString("Post title was changed to I changed the title for post 1\n");
        $this->em->flush();

        $this->assertCount(0, $entity->releaseAndResetEvents());
    }



    /**
     * @var EntityManager
     */
    protected $em;

    protected function setUp()
    {
        $evm = new EventManager();
        $evm->addEventSubscriber(new DomainEventPublisher());
        $evm->addEventSubscriber(new DomainEventListener());

        $conn = [
            'driver'   => $GLOBALS['DOCTRINE_DRIVER'],
            'memory'   => $GLOBALS['DOCTRINE_MEMORY'],
            'dbname'   => $GLOBALS['DOCTRINE_DATABASE'],
            'user'     => $GLOBALS['DOCTRINE_USER'],
            'password' => $GLOBALS['DOCTRINE_PASSWORD'],
            'host'     => $GLOBALS['DOCTRINE_HOST'],
        ];

        $driver = new YamlDriver([
            __DIR__ . '/../../config/posts',
            __DIR__ . '/../../config/embeds',
        ]);
        $config = new Configuration();
        $config->setMetadataCacheImpl(new ArrayCache());
        $config->setQueryCacheImpl(new ArrayCache());
        $config->setProxyDir(sys_get_temp_dir());
        $config->setProxyNamespace('AppBundle\Tests\Proxies');
        $config->setMetadataDriverImpl($driver);

        Type::overrideType(Type::DATETIME, DateTimeType::class);

        $em = EntityManager::create($conn, $config, $evm);

        $schemaTool = new SchemaTool($em);

        try {
            $schemaTool->createSchema([
                $em->getClassMetadata(Post::class),
                $em->getClassMetadata(Comment::class),
            ]);
        } catch (\Exception $e) {
            if (
                $GLOBALS['DOCTRINE_DRIVER'] != 'pdo_mysql' ||
                !($e instanceof \PDOException && strpos($e->getMessage(), 'Base table or view already exists') !== false)
            ) {
                throw $e;
            }
        }

        $this->em = $em;
    }

    protected function tearDown()
    {
        $this->em = null;
    }
}
