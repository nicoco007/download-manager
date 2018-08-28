<?php


namespace App\Controller;


use App\Entity\DownloadableFile;
use App\Entity\Project;
use App\Service\FileManager;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class AdminController extends AbstractController
{
    /**
     * @Route("/admin/login/", name="login")
     *
     * @param AuthenticationUtils $authenticationUtils
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function login(AuthenticationUtils $authenticationUtils)
    {
        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('admin/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error
        ]);
    }

    /**
     * @Route("/admin/", name="admin_home")
     */
    public function index()
    {
        return $this->render('admin/index.html.twig');
    }

    /**
     * @Route("/admin/projects/", name="projects")
     */
    public function projects()
    {
        $projects = $this->getDoctrine()->getRepository(Project::class)->findAll();

        return $this->render('admin/projects.html.twig', ['projects' => $projects]);
    }

    /**
     * @Route("/admin/projects/new/", name="add_project")
     * @param Request $request
     * @return Response
     */
    public function addProject(Request $request)
    {
        $project = new Project();

        $form = $this->createFormBuilder($project)
            ->add('name', TextType::class, ['required' => true])
            ->add('slug', TextType::class, ['required' => true])
            ->add('save', SubmitType::class, ['label' => 'Add'])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $doctrine = $this->getDoctrine();

            $entityManager = $doctrine->getManager();
            $entityManager->persist($project);
            $entityManager->flush();

            return $this->redirectToRoute('projects');
        }

        return $this->render('admin/projects/new.html.twig', ['form' => $form->createView()]);
    }

    /**
     * @Route("/admin/projects/edit/{id}", name="edit_project")
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function editProject(Request $request, $id)
    {
        $project = $this->getDoctrine()->getRepository(Project::class)->findOneBy(['id' => $id]);

        if ($project === null)
            throw new NotFoundHttpException();

        $form = $this->createFormBuilder($project)
            ->add('name', TextType::class, ['required' => true])
            ->add('slug', TextType::class, ['required' => true])
            ->add('save', SubmitType::class, ['label' => 'Save'])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $doctrine = $this->getDoctrine();

            $entityManager = $doctrine->getManager();
            $entityManager->persist($project);
            $entityManager->flush();

            return $this->redirectToRoute('projects');
        }

        return $this->render('admin/projects/edit.html.twig', ['form' => $form->createView()]);
    }

    /**
     * @Route("/admin/projects/delete/{id}", name="delete_project")
     * @param int $id
     * @return Response
     */
    public function deleteProject($id)
    {
        $doctrine = $this->getDoctrine();

        $project = $doctrine->getRepository(Project::class)->findOneBy(['id' => $id]);

        $manager = $doctrine->getManager();
        $manager->remove($project);
        $manager->flush();

        return $this->redirectToRoute('projects');
    }

    /**
     * @Route("/admin/projects/project/{slug}/", name="files")
     * @param $slug
     * @return Response
     */
    public function files($slug)
    {
        $doctrine = $this->getDoctrine();
        $project = $doctrine->getRepository(Project::class)->findOneBy(['slug' => $slug]);

        return $this->render('admin/files.html.twig', [
            'files' => $project->getFiles(),
            'project' => $project
        ]);
    }

    /**
     * @Route("/admin/projects/project/{slug}/upload/", name="upload")
     * @param Request $request
     * @param FileManager $fileManager
     * @param string $slug
     * @return Response
     */
    public function upload(Request $request, FileManager $fileManager, $slug)
    {
        $doctrine = $this->getDoctrine();

        /** @var Project $project */
        $project = $doctrine->getRepository(Project::class)->findOneBy(['slug' => $slug]);

        if ($project === null)
            throw new NotFoundHttpException();

        $file = new DownloadableFile();

        $form = $this->createFormBuilder($file)
            ->add('local_path', FileType::class, ['label' => 'File', 'required' => true])
            ->add('save', SubmitType::class, ['label' => 'Upload File'])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $file->setProject($project);

            /** @var UploadedFile $path */
            $path = $form->get('local_path')->getData();

            $file->setName($path->getClientOriginalName());
            $file->setLocalPath($fileManager->upload($path));
            $file->setUploadTime(new \DateTime());

            $entityManager = $doctrine->getManager();
            $entityManager->persist($file);
            $entityManager->flush();

            return $this->redirectToRoute('files', ['slug' => $project->getSlug()]);
        }

        return $this->render('admin/files/new.html.twig', ['form' => $form->createView()]);
    }

    /**
     * @Route("/admin/projects/project/{projectSlug}/files/{fileName}/edit", name="edit_file")
     * @param Request $request
     * @param string $projectSlug
     * @param string $fileName
     * @return Response
     */
    public function editFile(Request $request, string $projectSlug, string $fileName)
    {
        $doctrine = $this->getDoctrine();
        $repo = $doctrine->getRepository(DownloadableFile::class);

        $project = $doctrine->getRepository(Project::class)->findOneBy(['slug' => $projectSlug]);
        $file = $repo->findOneBy(['name' => $fileName, 'project' => $project]);

        $form = $this->createFormBuilder($file)
            ->add('name', TextType::class, ['required' => true])
            ->add('save', SubmitType::class, ['label' => 'Save Changes'])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $doctrine->getManager();
            $entityManager->persist($file);
            $entityManager->flush();

            return $this->redirectToRoute('files', ['slug' => $file->getProject()->getSlug()]);
        }

        return $this->render('admin/files/edit.html.twig', ['form' => $form->createView()]);
    }

    /**
     * @Route("/admin/projects/project/{projectSlug}/files/{fileName}/delete", name="delete_file")
     * @param string $projectSlug
     * @param string $fileName
     * @return Response
     */
    public function deleteFile(string $projectSlug, string $fileName)
    {
        $doctrine = $this->getDoctrine();
        $project = $doctrine->getRepository(Project::class)->findOneBy(['slug' => $projectSlug]);
        $file = $doctrine->getRepository(DownloadableFile::class)->findOneBy(['name' => $fileName, 'project' => $project]);

        $manager = $doctrine->getManager();
        $manager->remove($file);
        $manager->flush();

        return $this->redirectToRoute('files', ['slug' => $project->getSlug()]);
    }
}