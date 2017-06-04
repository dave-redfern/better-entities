<?php

namespace AppBundle\Repositories;

use AppBundle\Entities\Post;
use Doctrine\ORM\EntityRepository;

/**
 * Class PostRepository
 *
 * @package    AppBundle\Repositories
 * @subpackage AppBundle\Repositories\PostRepository
 */
class PostRepository extends EntityRepository
{

    /**
     * @param string $slug
     *
     * @return null|Post
     */
    public function findOneBySlug($slug)
    {
        $qb = $this->createQueryBuilder('p');
        $qb
            ->where('p.title.slug = :slug')
            ->setParameter(':slug', $slug)
            ->setMaxResults(1)
        ;

        return $qb->getQuery()->getOneOrNullResult();
    }
}