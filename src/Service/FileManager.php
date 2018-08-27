<?php


namespace App\Service;


use App\Entity\DownloadableFile;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class FileManager
{
    /** @var string */
    private $targetDirectory;

    /**
     * @param string $targetDirectory
     */
    public function __construct($targetDirectory)
    {
        $this->targetDirectory = $targetDirectory;
    }

    /**
     * @param UploadedFile $file
     * @return string
     */
    public function upload(UploadedFile $file): string
    {
        $fileName = hash('sha256', uniqid()) . '.' . ($file->guessExtension() ?? $file->getClientOriginalExtension());

        $file->move($this->getTargetDirectory(), $fileName);

        return $fileName;
    }

    /**
     * @param DownloadableFile $file
     */
    public function delete(DownloadableFile $file): void
    {
        $fs = new Filesystem();
        $fs->remove([$this->getTargetDirectory() . '/' . $file->getLocalPath()]);
    }

    /**
     * @param DownloadableFile $file
     * @return string
     */
    public function path(DownloadableFile $file): string {
        return $this->getTargetDirectory() . '/' . $file->getLocalPath();
    }

    /**
     * @return string
     */
    private function getTargetDirectory(): string
    {
        return $this->targetDirectory;
    }
}