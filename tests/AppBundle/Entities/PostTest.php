<?php

namespace AppBundle\Tests\Entities;

use AppBundle\Entities\Post;
use AppBundle\Events\CommentLeftOnPost;
use AppBundle\Events\PostContentChanged;
use AppBundle\Events\PostCreated;
use AppBundle\Events\PostPublished;
use AppBundle\Events\PostRemovedFromPublishedList;
use AppBundle\Events\PostTitleChanged;
use AppBundle\Support\Contracts\RaisesDomainEvents;
use AppBundle\ValueObjects\Commenter;
use AppBundle\ValueObjects\EmailAddress;
use AppBundle\ValueObjects\PostAuthor;
use AppBundle\ValueObjects\PostContent;
use AppBundle\ValueObjects\PostTitle;
use PHPUnit\Framework\TestCase;
use Somnambulist\Collection\Collection;

/**
 * Class PostTest
 *
 * @package    AppBundle\Tests\Entities
 * @subpackage AppBundle\Tests\Entities\PostTest
 */
class PostTest extends TestCase
{

    /**
     * @param RaisesDomainEvents $object
     * @param string             $event
     */
    protected function assertRaisesEvent(RaisesDomainEvents $object, $event)
    {
        $events = $object->releaseAndResetEvents();

        $this->assertTrue($this->assertContainsInstanceOf($events, $event));
    }

    /**
     * @param \Traversable $collection
     * @param string       $class
     *
     * @return bool
     */
    protected function assertContainsInstanceOf($collection, $class)
    {
        return Collection::collect($collection)->filter(function ($ele) use ($class) {
            return $ele instanceof $class;
        })->count() > 0;
    }

    /**
     * @group domain
     * @group entities
     * @group entities-post
     */
    public function testCreate()
    {
        $entity = Post::create(
            $pa = new PostAuthor('bob', new EmailAddress('bob@example.com')),
            $pt = new PostTitle('Test Post'),
            $pc = new PostContent('<p>This is a test post</p>')
        );

        $this->assertEquals($pa, $entity->author());
        $this->assertEquals($pt, $entity->title());
        $this->assertEquals($pc, $entity->content());
        $this->assertInstanceOf(\DateTimeImmutable::class, $entity->createdAt());
        $this->assertInstanceOf(\DateTimeImmutable::class, $entity->lastUpdated());
        $this->assertNull($entity->publishedAt());
    }

    /**
     * @group domain
     * @group entities
     * @group entities-post
     */
    public function testCreateAndPublish()
    {
        $entity = Post::createAndPublish(
            $pa = new PostAuthor('bob', new EmailAddress('bob@example.com')),
            $pt = new PostTitle('Test Post'),
            $pc = new PostContent('<p>This is a test post</p>')
        );

        $this->assertEquals($pa, $entity->author());
        $this->assertEquals($pt, $entity->title());
        $this->assertEquals($pc, $entity->content());
        $this->assertInstanceOf(\DateTimeImmutable::class, $entity->createdAt());
        $this->assertInstanceOf(\DateTimeImmutable::class, $entity->lastUpdated());
        $this->assertInstanceOf(\DateTimeImmutable::class, $entity->publishedAt());
        $this->assertTrue($entity->isPublished());
        $this->assertTrue($entity->isRecentlyPublished());
    }

    /**
     * @group domain
     * @group entities
     * @group entities-post
     */
    public function testCanPublishAtASpecificDateTime()
    {
        $entity = Post::create(
            $pa = new PostAuthor('bob', new EmailAddress('bob@example.com')),
            $pt = new PostTitle('Test Post'),
            $pc = new PostContent('<p>This is a test post</p>')
        );
        $entity->publish(new \DateTimeImmutable('-15 days'));

        $this->assertInstanceOf(\DateTimeImmutable::class, $entity->publishedAt());
        $this->assertTrue($entity->isPublished());
        $this->assertFalse($entity->isRecentlyPublished());
    }

    /**
     * @group domain
     * @group entities
     * @group entities-post
     */
    public function testCreatingRaisesDomainEvents()
    {
        $entity = Post::createAndPublish(
            $pa = new PostAuthor('bob', new EmailAddress('bob@example.com')),
            $pt = new PostTitle('Test Post'),
            $pc = new PostContent('<p>This is a test post</p>')
        );

        $this->assertRaisesEvent($entity, PostCreated::class);
    }

    /**
     * @group domain
     * @group entities
     * @group entities-post
     */
    public function testPublishingRaisesDomainEvents()
    {
        $entity = Post::createAndPublish(
            $pa = new PostAuthor('bob', new EmailAddress('bob@example.com')),
            $pt = new PostTitle('Test Post'),
            $pc = new PostContent('<p>This is a test post</p>')
        );

        $this->assertRaisesEvent($entity, PostPublished::class);
    }

    /**
     * @group domain
     * @group entities
     * @group entities-post
     */
    public function testUnpublishingRaisesDomainEvents()
    {
        $entity = Post::createAndPublish(
            $pa = new PostAuthor('bob', new EmailAddress('bob@example.com')),
            $pt = new PostTitle('Test Post'),
            $pc = new PostContent('<p>This is a test post</p>')
        );
        $entity->removeFromPublication();

        $this->assertRaisesEvent($entity, PostRemovedFromPublishedList::class);
    }

    /**
     * @group domain
     * @group entities
     * @group entities-post
     */
    public function testChangingTitleRaisesDomainEvents()
    {
        $entity = Post::create(
            $pa = new PostAuthor('bob', new EmailAddress('bob@example.com')),
            $pt = new PostTitle('Test Post'),
            $pc = new PostContent('<p>This is a test post</p>')
        );
        $entity->changeTitle(new PostTitle('Another Title'));

        $this->assertRaisesEvent($entity, PostTitleChanged::class);
    }

    /**
     * @group domain
     * @group entities
     * @group entities-post
     */
    public function testChangingContentRaisesDomainEvents()
    {
        $entity = Post::create(
            $pa = new PostAuthor('bob', new EmailAddress('bob@example.com')),
            $pt = new PostTitle('Test Post'),
            $pc = new PostContent('<p>This is a test post</p>')
        );
        $entity->replaceContentWith(new PostContent('<p>This post has had its content changed.</p>'));

        $this->assertRaisesEvent($entity, PostContentChanged::class);
    }

    /**
     * @group domain
     * @group entities
     * @group entities-post
     */
    public function testCanLeaveAComment()
    {
        $entity = Post::create(
            $pa = new PostAuthor('bob', new EmailAddress('bob@example.com')),
            $pt = new PostTitle('Test Post'),
            $pc = new PostContent('<p>This is a test post</p>')
        );
        $entity->leaveComment(new Commenter('Bob', new EmailAddress('bob@example.com')), 'These are some comments.');

        $this->assertCount(1, $entity->comments());
        $this->assertEquals('These are some comments.', $entity->comments()->first()->comments());
    }

    /**
     * @group domain
     * @group entities
     * @group entities-post
     */
    public function testCanFindCommentsBy()
    {
        $entity = Post::create(
            $pa = new PostAuthor('bob', new EmailAddress('bob@example.com')),
            $pt = new PostTitle('Test Post'),
            $pc = new PostContent('<p>This is a test post</p>')
        );
        $entity->leaveComment($comm = new Commenter('Bob', new EmailAddress('bob@example.com')), 'These are some comments.');
        $entity->leaveComment(new Commenter('Bob Marley', new EmailAddress('bob@example.com')), 'These are some comments.');
        $entity->leaveComment(new Commenter('Bob Bar', new EmailAddress('bob@example.com')), 'These are some comments.');
        $entity->leaveComment(new Commenter('Bob', new EmailAddress('bob@example.com')), 'Comments.');

        $this->assertCount(2, $entity->findCommentsBy($comm));
        $this->assertEquals('These are some comments.', $entity->comments()->first()->comments());
        $this->assertEquals('Comments.', $entity->comments()->last()->comments());
    }

    /**
     * @group domain
     * @group entities
     * @group entities-post
     */
    public function testCanFindCommentsContaining()
    {
        $entity = Post::create(
            $pa = new PostAuthor('bob', new EmailAddress('bob@example.com')),
            $pt = new PostTitle('Test Post'),
            $pc = new PostContent('<p>This is a test post</p>')
        );
        $entity->leaveComment(new Commenter('Bob', new EmailAddress('bob@example.com')), 'These are some comments.');
        $entity->leaveComment(new Commenter('Bob Marley', new EmailAddress('bob@example.com')), 'These are some more comments.');
        $entity->leaveComment(new Commenter('Bob Bar', new EmailAddress('bob@example.com')), 'These are some other comments.');
        $entity->leaveComment(new Commenter('Bob', new EmailAddress('bob@example.com')), 'Comments.');

        $this->assertCount(4, $entity->findCommentsContaining('comment'));
        $this->assertEquals('These are some comments.', $entity->comments()->first()->comments());
        $this->assertEquals('Comments.', $entity->comments()->last()->comments());
    }

    /**
     * @group domain
     * @group entities
     * @group entities-post
     */
    public function testCanReverseCommentOrder()
    {
        $entity = Post::create(
            $pa = new PostAuthor('bob', new EmailAddress('bob@example.com')),
            $pt = new PostTitle('Test Post'),
            $pc = new PostContent('<p>This is a test post</p>')
        );
        $entity->leaveComment(new Commenter('Bob', new EmailAddress('bob@example.com')), 'These are some comments.');
        $entity->leaveComment(new Commenter('Bob Marley', new EmailAddress('bob@example.com')), 'These are some more comments.');
        $entity->leaveComment(new Commenter('Bob Bar', new EmailAddress('bob@example.com')), 'These are some other comments.');
        $entity->leaveComment(new Commenter('Bob', new EmailAddress('bob@example.com')), 'Comments.');

        $comments = $entity->reverseCommentOrder();

        $this->assertCount(4, $comments);
        $this->assertEquals('These are some comments.', $comments->last()->comments());
        $this->assertEquals('Comments.', $comments->first()->comments());
    }

    /**
     * @group domain
     * @group entities
     * @group entities-post
     */
    public function testLeavingACommentContentRaisesDomainEvents()
    {
        $entity = Post::create(
            $pa = new PostAuthor('bob', new EmailAddress('bob@example.com')),
            $pt = new PostTitle('Test Post'),
            $pc = new PostContent('<p>This is a test post</p>')
        );
        $entity->leaveComment(new Commenter('Bob', new EmailAddress('bob@example.com')), 'These are some comments.');

        $this->assertRaisesEvent($entity, CommentLeftOnPost::class);
    }
}
