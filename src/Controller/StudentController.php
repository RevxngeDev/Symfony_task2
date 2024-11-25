<?php
namespace App\Controller;

use App\Entity\Grade;
use App\Entity\Student;
use App\Form\GradeType;
use App\Form\StudentType;
use App\Repository\StudentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class StudentController extends AbstractController
{
    #[Route('/', name: 'student_index', methods: ['GET', 'POST'])]
    public function index(
        Request $request,
        StudentRepository $studentRepository,
        EntityManagerInterface $entityManager
    ): Response {
        // Fetch all students
        $students = $studentRepository->findAll();

        // Add new student form
        $newStudent = new Student();
        $studentForm = $this->createForm(StudentType::class, $newStudent);
        $studentForm->handleRequest($request);

        if ($studentForm->isSubmitted() && $studentForm->isValid()) {
            $entityManager->persist($newStudent);
            $entityManager->flush();

            return $this->redirectToRoute('student_index');
        }

        // Handle grade submission
        if ($request->isMethod('POST') && $request->request->get('student_id')) {
            $studentId = $request->request->get('student_id');
            $gradeValue = $request->request->get('grade');

            $student = $studentRepository->find($studentId);
            if ($student) {
                $grade = new Grade();
                $grade->setValue($gradeValue);
                $grade->setStudent($student);

                $entityManager->persist($grade);
                $entityManager->flush();

                return $this->redirectToRoute('student_index');
            }
        }

        return $this->render('student/index.html.twig', [
            'students' => $students,
            'studentForm' => $studentForm->createView(),
        ]);
    }
}


