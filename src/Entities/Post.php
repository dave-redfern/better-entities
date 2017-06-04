<?php

namespace AppBundle\Entities;

use AppBundle\Events;
use AppBundle\Support\Contracts\RaisesDomainEvents as RaisesDomainEventsContract;
use AppBundle\Support\Traits\RaisesDomainEvents;
use AppBundle\ValueObjects\Commenter;
use AppBundle\ValueObjects\PostAuthor;
use AppBundle\ValueObjects\PostContent;
use AppBundle\ValueObjects\PostTitle;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

/**
 * Class Post
 *
 * @package    AppBundle\Entities
 * @subpackage AppBundle\Entities\Post
 */
class Post implements RaisesDomainEventsContract
{

    use RaisesDomainEvents;

    const NUM_ITEMS = 10;

    /**
     * @var int
     */
    private $id;

    /**
     * @var PostAuthor
     */
    private $author;

    /**
     * @var PostTitle
     */
    private $title;

    /**
     * @var PostContent
     */
    private $content;

    /**
     * @var DateTimeImmutable
     */
    private $createdAt;

    /**
     * @var DateTimeImmutable
     */
    private $updatedAt;

    /**
     * @var DateTimeImmutable|null
     */
    private $publishedAt;

    /**
     * @var Collection|Comment[]
     */
    private $comments;



    /**
     * Constructor.
     *
     * @param PostAuthor  $author
     * @param PostTitle   $title
     * @param PostContent $content
     */
    private function __construct(PostAuthor $author, PostTitle $title, PostContent $content)
    {
        $this->author    = $author;
        $this->title     = $title;
        $this->content   = $content;
        $this->createdAt = new DateTimeImmutable();
        $this->updatedAt = new DateTimeImmutable();
        $this->comments  = new ArrayCollection();
    }

    /**
     * @param PostAuthor  $author
     * @param PostTitle   $title
     * @param PostContent $content
     *
     * @return static
     */
    public static function create(PostAuthor $author, PostTitle $title, PostContent $content)
    {
        $entity = new static($author, $title, $content);
        $entity->raise(new Events\PostCreated([
            'author' => $author, 'title' => $title, 'created_at' => $entity->createdAt(),
        ]));

        return $entity;
    }

    /**
     * @param PostAuthor  $author
     * @param PostTitle   $title
     * @param PostContent $content
     *
     * @return static
     */
    public static function createAndPublish(PostAuthor $author, PostTitle $title, PostContent $content)
    {
        $entity = static::create($author, $title, $content);
        $entity->publish();

        return $entity;
    }

    /**
     * @param DateTimeImmutable|null $publishedAt
     */
    public function publish(DateTimeImmutable $publishedAt = null)
    {
        $this->publishedAt = ($publishedAt ?: new DateTimeImmutable());
        $this->updatedAt   = new DateTimeImmutable();

        $this->raise(new Events\PostPublished([
            'author' => $this->author, 'title' => $this->title, 'published_at' => $this->publishedAt(),
        ]));
    }

    /**
     * Remove this post from the published posts
     */
    public function removeFromPublication()
    {
        $this->publishedAt = null;
        $this->updatedAt   = new DateTimeImmutable();

        $this->raise(new Events\PostRemovedFromPublishedList([
            'author' => $this->author, 'title' => $this->title, 'removed_at' => new DateTimeImmutable(),
        ]));
    }

    /**
     * @param PostTitle $title
     */
    public function changeTitle(PostTitle $title)
    {
        $this->title     = $title;
        $this->updatedAt = new DateTimeImmutable();

        $this->raise(new Events\PostTitleChanged([
            'author' => $this->author, 'title' => $this->title, 'updated_at' => new DateTimeImmutable(),
        ]));
    }

    /**
     * @param PostContent $content
     */
    public function replaceContentWith(PostContent $content)
    {
        $this->content   = $content;
        $this->updatedAt = new DateTimeImmutable();

        $this->raise(new Events\PostContentChanged([
            'author' => $this->author, 'title' => $this->title, 'updated_at' => new DateTimeImmutable(),
        ]));
    }


    /**
     * @return PostAuthor
     */
    public function author(): PostAuthor
    {
        return $this->author;
    }

    /**
     * @return PostTitle
     */
    public function title(): PostTitle
    {
        return $this->title;
    }

    /**
     * @return PostContent
     */
    public function content(): PostContent
    {
        return $this->content;
    }

    /**
     * @return DateTimeImmutable
     */
    public function createdAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    /**
     * @return DateTimeImmutable
     */
    public function lastUpdated(): DateTimeImmutable
    {
        return $this->updatedAt;
    }

    /**
     * @return DateTimeImmutable|null
     */
    public function publishedAt(): ?DateTimeImmutable
    {
        return $this->publishedAt;
    }

    /**
     * @return bool
     */
    public function isPublished(): bool
    {
        return $this->publishedAt instanceof DateTimeImmutable;
    }

    /**
     * @return bool
     */
    public function isRecentlyPublished(): bool
    {
        return $this->isPublished() && $this->publishedAt->diff(new DateTimeImmutable())->days < 10;
    }



    /**
     * @return Comment[]|Collection
     */
    public function comments(): Collection
    {
        return new ArrayCollection($this->comments->toArray());
    }

    /**
     * @return Comment[]|Collection
     */
    public function reverseCommentOrder(): Collection
    {
        return new ArrayCollection(array_reverse($this->comments->toArray()));
    }

    /**
     * @param Commenter $commenter
     *
     * @return Collection
     */
    public function findCommentsBy(Commenter $commenter): Collection
    {
        return $this->comments->filter(function ($comment) use ($commenter) {
            /** @var Comment $comment */
            return $comment->commenter()->equals($commenter);
        });
    }

    /**
     * @param string $keyword
     *
     * @return Collection
     */
    public function findCommentsContaining(string $keyword): Collection
    {
        return $this->comments->filter(function ($comment) use ($keyword) {
            /** @var Comment $comment */
            return $comment->contains($keyword);
        });
    }

    /**
     * @param Commenter $commenter
     * @param string    $comment
     */
    public function leaveComment(Commenter $commenter, string $comment)
    {
        $this->comments->add(new Comment($this, $commenter, $comment));
        $this->updatedAt = new DateTimeImmutable();

        $this->raise(new Events\CommentLeftOnPost([
            'title'      => $this->title,
            'commenter'  => $commenter,
            'comment'    => $comment,
            'created_at' => new DateTimeImmutable(),
        ]));
    }
}
