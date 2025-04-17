<?php

declare(strict_types=1);

namespace App\Infrastructure\Http\Teacher\Controller;

//use App\Application\Teacher\CreateTeacherUseCase;

use App\Domain\Entity\Teacher;
use App\Domain\Repository\TeacherRepository;
use App\Enums\Genders;
use App\Infrastructure\Services\TeacherService;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class TeacherController extends AbstractController
{
//    public function __construct(
//        private CreateTeacherUseCase $createTeacherUseCase
//    ){}

    public function create(EntityManagerInterface $em)
    {
        //TODO move to services and repos
        $teacher = (new Teacher())
            ->setName('Oleg1')
            ->setAge(35)
            ->setGender(Genders::MALE)
            ->setRating(5);

        $em->persist($teacher);
        $em->flush();

        exit();
    }
    public function update(TeacherRepository $teacherRepository, EntityManagerInterface $em, int $id)
    {
        $teacher = $teacherRepository->findOneBy(['id' => $id]);

        if (!$teacher) {
            throw new \RuntimeException("Teacher with id = $id not found");
        }

        $name = 'Ivan';
        $age = 55;
        $gender = Genders::FEMALE;
        $rating = 10;

        $teacher->setName($name)
            ->setAge($age)
            ->setGender($gender)
            ->setRating($rating);

        $em->flush();

        exit();
    }
    public function delete(TeacherRepository $teacherRepository, EntityManagerInterface $em, int $id)
    {
        $teacher = $teacherRepository->findOneBy(['id' => $id]);
        if (!$teacher) {
            throw new \RuntimeException("Teacher with id = $id not found");
        }

        $em->remove($teacher);
        $em->flush();

        exit();
    }
    public function getById(TeacherRepository $teacherRepository, int $id)
    {
        //TODO move to services and repos


        $teacher = $teacherRepository->findOneBy(['id' => $id]);

        //echo $wewe;

        exit();
    }

    public function getAll(TeacherRepository $teacherRepository)
    {
        //TODO move to services and repos

        $allTeaches = $teacherRepository->findAll();

        //echo $wewe;

        exit();
    }


//    public function getTeachersList(): Response
//    {
//        return $this->render('teachers.html.twig');
//    }
}
