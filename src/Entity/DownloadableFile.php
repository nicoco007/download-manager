<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\PersistentCollection;

/**
 * @ORM\Entity(repositoryClass="App\Repository\FileRepository")
 * @ORM\Table(name="downloadable_file", uniqueConstraints={@ORM\UniqueConstraint(name="unique_file_in_project", columns={"name", "project_id"})})
 */
class DownloadableFile
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @var int
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @var string
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255)
     * @var string
     */
    private $local_path;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Project", inversedBy="files")
     * @ORM\JoinColumn(name="project_id", referencedColumnName="id", nullable=false)
     * @var Project
     */
    private $project;

    /**
     * @ORM\Column(type="datetime")
     * @var \DateTime
     */
    private $uploadTime;


    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Download", mappedBy="file", cascade={"remove"})
     * @var PersistentCollection
     */
    private $downloads;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getLocalPath(): ?string
    {
        return $this->local_path;
    }

    public function setLocalPath(string $local_path): self
    {
        $this->local_path = $local_path;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getUploadTime(): \DateTime
    {
        return $this->uploadTime;
    }

    /**
     * @param mixed $uploadTime
     */
    public function setUploadTime(\DateTime $uploadTime): void
    {
        $this->uploadTime = $uploadTime;
    }

    /**
     * @return mixed
     */
    public function getDownloads(): PersistentCollection
    {
        return $this->downloads;
    }

    /**
     * @return Project
     */
    public function getProject(): Project
    {
        return $this->project;
    }

    /**
     * @param Project $project
     */
    public function setProject(Project $project): void
    {
        $this->project = $project;
    }
}
