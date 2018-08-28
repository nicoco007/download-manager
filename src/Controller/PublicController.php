<?php


namespace App\Controller;


use App\Entity\Download;
use App\Entity\DownloadableFile;
use App\Entity\Project;
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
        return $this->render('index.html.twig');
    }

    /**
     * @Route("/download/{projectSlug}/{fileName}", name="download", requirements={"path"=".+"})
     *
     * @param FileManager $fileUploader
     * @param string $projectSlug
     * @param string $fileName
     * @return Response
     */
    public function download(FileManager $fileUploader, string $projectSlug, string $fileName)
    {
        $doctrine = $this->getDoctrine();

        /** @var Project $project */
        $project = $doctrine->getRepository(Project::class)->findOneBy(['slug' => $projectSlug]);

        /** @var DownloadableFile $file */
        $file = $doctrine->getRepository(DownloadableFile::class)->findOneBy(['project' => $project, 'name' => $fileName]);

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