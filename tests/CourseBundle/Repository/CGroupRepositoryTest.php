<?php

declare(strict_types=1);

/* For licensing terms, see /license.txt */

namespace Chamilo\Tests\CourseBundle\Repository;

use Chamilo\CourseBundle\Entity\CGroup;
use Chamilo\CourseBundle\Repository\CGroupRepository;
use Chamilo\Tests\AbstractApiTest;
use Chamilo\Tests\ChamiloTestTrait;

class CGroupRepositoryTest extends AbstractApiTest
{
    use ChamiloTestTrait;

    public function testCreate(): void
    {
        self::bootKernel();

        $em = $this->getEntityManager();
        $repo = self::getContainer()->get(CGroupRepository::class);

        $course = $this->createCourse('new');
        $teacher = $this->createUser('teacher');

        $item = (new CGroup())
            ->setName('Group')
            ->setParent($course)
            ->setCreator($teacher)
            ->setMaxStudent(100)

        ;
        $this->assertHasNoEntityViolations($item);
        $em->persist($item);
        $em->flush();

        $this->assertSame(1, $repo->count([]));
        $this->assertNotNull($repo->findOneByTitle('Group'));
    }

    public function testFindAllByCourse(): void
    {
        self::bootKernel();

        $repo = self::getContainer()->get(CGroupRepository::class);

        $course = $this->createCourse('new');
        $teacher = $this->createUser('teacher');

        $item = (new CGroup())
            ->setName('Group')
            ->setParent($course)
            ->setCreator($teacher)
            ->setMaxStudent(100)
            ->addCourseLink($course)
        ;
        $repo->create($item);

        $qb = $repo->findAllByCourse($course);
        $this->assertSame(1, \count($qb->getQuery()->getResult()));
    }
}
