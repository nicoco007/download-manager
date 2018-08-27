<?php


namespace App\Controller;


use App\Entity\Download;
use App\Entity\DownloadableFile;
use App\Entity\Folder;
use App\Service\FileManager;
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
     * @Route("/download/{path}", name="download", requirements={"path"=".+"})
     *
     * @param FileManager $fileUploader
     * @param string $path
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function download(FileManager $fileUploader, string $path)
    {
        $doctrine = $this->getDoctrine();
        $parts = explode('/', $path);
        $partsCount = count($parts);

        /** @var Folder $folder */
        $folder = null;

        /** @var DownloadableFile $file */
        $file = null;

        for ($i = 0; $i < $partsCount - 1; $i++) {
            $folder = $doctrine->getRepository(Folder::class)->findOneBy(['name' => $parts[$i]]);

            if ($folder === null)
                throw new NotFoundHttpException();
        }

        $file = $doctrine->getRepository(DownloadableFile::class)->findOneBy(['name' => $parts[$partsCount - 1], 'folder' => $folder]);

        if ($file === null)
            throw new NotFoundHttpException();

        $manager = $doctrine->getManager();

        $download = new Download();
        $download->setFile($file);
        $download->setTime(new \DateTime());

        $manager->persist($download);
        $manager->flush();

        return $this->file($fileUploader->path($file), $file->getName());
    }
}