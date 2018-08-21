<?php


namespace App\Controller;


use App\Entity\Download;
use App\Entity\DownloadableFile;
use App\Entity\FileGroup;
use App\Service\FileUploader;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

class PublicController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function index()
    {
        return new Response('nope');
    }

    /**
     * @Route("/downloads/{groupSlug}/", name="group_files")
     *
     * @param string $groupSlug
     * @return Response
     */
    public function listGroupFiles(string $groupSlug)
    {
        /** @var FileGroup $group */
        $group = $this->getDoctrine()->getRepository(FileGroup::class)->findOneBy(['slug' => $groupSlug]);

        if ($group === null)
            throw new NotFoundHttpException();

        return $this->render('group-files.html.twig', ['group' => $group, 'files' => $group->getFiles()]);
    }

    /**
     * @Route("/downloads/{groupSlug}/{fileName}", name="download")
     *
     * @param FileUploader $fileUploader
     * @param string $groupSlug
     * @param string $fileName
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function download(FileUploader $fileUploader, string $groupSlug, string $fileName)
    {
        $doctrine = $this->getDoctrine();

        /** @var FileGroup $group */
        $group = $doctrine->getRepository(FileGroup::class)->findOneBy(['slug' => $groupSlug]);

        /** @var DownloadableFile $file */
        $file = $doctrine->getRepository(DownloadableFile::class)->findOneBy(['name' => $fileName, 'group' => $group]);

        if ($file === null)
            throw new NotFoundHttpException();

        $manager = $doctrine->getManager();

        $download = new Download();
        $download->setFile($file);
        $download->setTime(new \DateTime());

        $manager->persist($download);
        $manager->flush();

        return $this->file($fileUploader->getTargetDirectory() . '/' . $file->getPath(), $file->getName());
    }
}