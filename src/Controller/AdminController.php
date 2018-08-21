<?php


namespace App\Controller;


use App\Entity\DownloadableFile;
use App\Entity\FileGroup;
use App\Service\FileUploader;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
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
        /*$entityManager = $this->getDoctrine()->getManager();

        $download = new Download();
        $download->setFileName("something.zip");
        $download->setTime(new \DateTime());

        $entityManager->persist($download);

        $entityManager->flush();*/

        return $this->render('admin/index.html.twig');
    }

    /**
     * @Route("/admin/files/", name="files")
     */
    public function files()
    {
        $repository = $this->getDoctrine()->getRepository(DownloadableFile::class);
        $files = $repository->findAll();

        return $this->render('admin/files.html.twig', ['files' => $files]);
    }

    /**
     * @Route("/admin/files/new/", name="add_file")
     *
     * @param Request $request
     * @param FileUploader $fileUploader
     * @return Response
     */
    public function newFile(Request $request, FileUploader $fileUploader)
    {
        $file = new DownloadableFile();

        $form = $this->createFormBuilder($file)
            ->add('group', EntityType::class, ['class' => FileGroup::class, 'choice_label' => 'name', 'required' => true])
            ->add('path', FileType::class, ['label' => 'File', 'required' => true])
            ->add('save', SubmitType::class, ['label' => 'Upload File'])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($file->getGroup() === null)
                $form->addError(new FormError('You must select a group.'));

            if ($form->isValid()) {
                /** @var UploadedFile $path */
                $path = $form->get('path')->getData();

                $file->setName($path->getClientOriginalName());
                $file->setPath($fileUploader->upload($path));
                $file->setUploadTime(new \DateTime());

                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($file);
                $entityManager->flush();

                return $this->redirectToRoute('files');
            }
        }

        return $this->render('admin/files/new.html.twig', ['form' => $form->createView()]);
    }

    /**
     * @Route("/admin/files/edit/{id}", name="edit_file")
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function editFile(Request $request, int $id)
    {
        $repo = $this->getDoctrine()->getRepository(DownloadableFile::class);
        $file = $repo->findOneBy(['id' => $id]);

        $form = $this->createFormBuilder($file)
            ->add('name', TextType::class, ['required' => true])
            ->add('group', EntityType::class, ['class' => FileGroup::class, 'choice_label' => 'name', 'required' => true])
            ->add('save', SubmitType::class, ['label' => 'Save File'])
            ->add('delete', SubmitType::class, ['label' => 'Delete'])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();

            if ($form->get('delete')->isClicked()) {
                $entityManager->remove($file);
            } else {
                $entityManager->persist($file);
            }

            $entityManager->flush();

            return $this->redirectToRoute('files');
        }

        return $this->render('admin/files/edit.html.twig', ['form' => $form->createView()]);
    }

    /**
     * @Route("/admin/groups/", name="groups")
     */
    public function groups()
    {
        $repo = $this->getDoctrine()->getRepository(FileGroup::class);
        $groups = $repo->findAll();

        return $this->render('admin/groups.html.twig', ['groups' => $groups]);
    }

    /**
     * @Route("/admin/groups/new/", name="add_group")
     *
     * @param Request $request
     * @return Response
     */
    public function newGroup(Request $request)
    {
        $group = new FileGroup();

        $form = $this->createFormBuilder($group)
            ->add('name', TextType::class, ['required' => true])
            ->add('slug', TextType::class)
            ->add('save', SubmitType::class, ['label' => 'Add Group'])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($group->getSlug() === null || $group->getSlug() === "")
                $group->setSlug($group->getName()); // TODO: strip non-ascii and replace spaces with dashes?

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($group);
            $entityManager->flush();

            return $this->redirectToRoute('groups');
        }

        return $this->render('admin/groups/new.html.twig', ['form' => $form->createView()]);
    }

    /**
     * @Route("/admin/groups/edit/{id}", name="edit_group")
     *
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function editGroup(Request $request, int $id)
    {
        $repo = $this->getDoctrine()->getRepository(FileGroup::class);
        $group = $repo->findOneBy(['id' => $id]);

        $form = $this->createFormBuilder($group)
            ->add('name', TextType::class, ['required' => true])
            ->add('slug', TextType::class)
            ->add('save', SubmitType::class, ['label' => 'Save Group'])
            ->add('delete', SubmitType::class, ['label' => 'Delete'])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($group->getSlug() === null || $group->getSlug() === "")
                $group->setSlug($group->getName()); // TODO: strip non-ascii and replace spaces with dashes?

            $entityManager = $this->getDoctrine()->getManager();

            if ($form->get('delete')->isClicked())
                $entityManager->remove($group);
            else
                $entityManager->persist($group);

            $entityManager->flush();

            return $this->redirectToRoute('groups');
        }

        return $this->render('admin/groups/new.html.twig', ['form' => $form->createView()]);
    }
}