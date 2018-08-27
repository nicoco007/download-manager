<?php


namespace App\Controller;


use App\Entity\DownloadableFile;
use App\Entity\Folder;
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
     * @Route("/admin/files/{path}", name="files", requirements={"path"=".+"}, defaults={"path"=""})
     * @param $path
     * @return Response
     */
    public function files($path)
    {
        $folder = null;
        $folderPath = '';
        $currentFolderId = null;

        if ($path !== '') {
            $folder = $this->getFolderFromPath($path);

            if ($folder === null)
                throw new NotFoundHttpException();

            $folderPath = $folder->getPath() . '/';
            $currentFolderId = $folder->getId();
            $folders = $folder->getFolders();
            $files = $folder->getFiles();
        } else {
            $doctrine = $this->getDoctrine();

            $folders = $doctrine->getRepository(Folder::class)->findBy(['parent' => null]);
            $files = $doctrine->getRepository(DownloadableFile::class)->findBy(['folder' => null]);
        }

        $folderHierarchy = [];
        $current = $folder;

        while ($current !== null) {
            $folderHierarchy[] = $current;
            $current = $current->getParent();
        }

        $folderHierarchy = array_reverse($folderHierarchy);

        return $this->render('admin/files.html.twig', [
            'files' => $files,
            'folders' => $folders,
            'currentFolder' => $folder,
            'folderPath' => $folderPath,
            'folderHierarchy' => $folderHierarchy,
            'currentFolderId' => $currentFolderId
        ]);
    }

    private function getFolderFromPath(string $path): ?Folder
    {
        $parts = explode('/', $path);
        $folder = null;
        $repository = $this->getDoctrine()->getRepository(Folder::class);

        foreach ($parts as $part) {
            /** @var Folder $folder */
            $folder = $repository->findOneBy(['parent' => $folder, 'name' => $part]);

            if ($folder === null)
                return null;
        }

        return $folder;
    }

    /**
     * @Route("/admin/upload/{parentId}", name="add_file", defaults={"parentId"=null})
     *
     * @param Request $request
     * @param FileManager $fileUploader
     * @param $parentId
     * @return Response
     */
    public function newFile(Request $request, FileManager $fileUploader, $parentId)
    {
        $file = new DownloadableFile();

        $form = $this->createFormBuilder($file)
            ->add('local_path', FileType::class, ['label' => 'File', 'required' => true])
            ->add('save', SubmitType::class, ['label' => 'Upload File'])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $doctrine = $this->getDoctrine();

            $file->setFolder($doctrine->getRepository(Folder::class)->findOneBy(['id' => $parentId]));

            /** @var UploadedFile $path */
            $path = $form->get('local_path')->getData();

            $file->setName($path->getClientOriginalName());
            $file->setLocalPath($fileUploader->upload($path));
            $file->setUploadTime(new \DateTime());

            $entityManager = $doctrine->getManager();
            $entityManager->persist($file);
            $entityManager->flush();

            return $this->redirectToRoute('files', ['path' => $file->getFolderPath()]);
        }

        return $this->render('admin/files/new.html.twig', ['form' => $form->createView()]);
    }

    /**
     * @Route("/admin/edit/{id}", name="edit_file")
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function editFile(Request $request, int $id)
    {
        $doctrine = $this->getDoctrine();
        $repo = $doctrine->getRepository(DownloadableFile::class);
        $file = $repo->findOneBy(['id' => $id]);

        $form = $this->createFormBuilder($file)
            ->add('name', TextType::class, ['required' => true])
            ->add('save', SubmitType::class, ['label' => 'Save Changes'])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $doctrine->getManager();
            $entityManager->persist($file);
            $entityManager->flush();

            return $this->redirectToRoute('files', ['path' => $file->getFolder() !== null ? $file->getFolder()->getPath() : '']);
        }

        return $this->render('admin/files/edit.html.twig', ['form' => $form->createView()]);
    }

    /**
     * @Route("/admin/delete/{id}", name="delete_file")
     * @param $id
     * @return Response
     */
    public function deleteFile($id)
    {
        $doctrine = $this->getDoctrine();
        $file = $doctrine->getRepository(DownloadableFile::class)->findOneBy(['id' => $id]);
        $filePath = $file->getFolder() !== null ? $file->getFolder()->getPath() : '';

        $manager = $doctrine->getManager();
        $manager->remove($file);
        $manager->flush();

        return $this->redirectToRoute('files', ['path' => $filePath]);
    }

    /**
     * @Route("/admin/folders/new/{parentId}", name="add_folder", defaults={"parentId"=null})
     *
     * @param Request $request
     * @param int $parentId
     * @return Response
     */
    public function newFolder(Request $request, ?int $parentId)
    {
        $folder = new Folder();

        $form = $this->createFormBuilder($folder)
            ->add('name', TextType::class, ['required' => true])
            ->add('save', SubmitType::class, ['label' => 'Add Folder'])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $doctrine = $this->getDoctrine();

            $folder->setParent($doctrine->getRepository(Folder::class)->findOneBy(['id' => $parentId]));

            $entityManager = $doctrine->getManager();
            $entityManager->persist($folder);
            $entityManager->flush();

            return $this->redirectToRoute('files', ['path' => $folder->getPath()]);
        }

        return $this->render('admin/groups/new.html.twig', ['form' => $form->createView()]);
    }

    /**
     * @Route("/admin/folders/edit/{id}", name="edit_folder")
     *
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function editFolder(Request $request, int $id)
    {
        $doctrine = $this->getDoctrine();
        $repo = $doctrine->getRepository(Folder::class);
        $folder = $repo->findOneBy(['id' => $id]);

        $form = $this->createFormBuilder($folder)
            ->add('name', TextType::class, ['required' => true])
            ->add('save', SubmitType::class, ['label' => 'Save Changes'])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $doctrine->getManager();
            $entityManager->persist($folder);
            $entityManager->flush();

            return $this->redirectToRoute('files');
        }

        return $this->render('admin/groups/new.html.twig', ['form' => $form->createView()]);
    }

    /**
     * @Route("/folders/delete/{id}", name="delete_folder")
     * @param $id
     * @return Response
     */
    public function deleteFolder($id)
    {
        $doctrine = $this->getDoctrine();
        $folder = $doctrine->getRepository(Folder::class)->findOneBy(['id' => $id]);
        $folderPath = $folder->getParent() !== null ? $folder->getParent()->getPath() : '';

        $manager = $doctrine->getManager();
        $manager->remove($folder);
        $manager->flush();

        return $this->redirectToRoute('files', ['path' => $folderPath]);
    }
}