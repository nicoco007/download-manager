<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\PersistentCollection;

/**
 * @ORM\Entity(repositoryClass="App\Repository\FileRepository")
 * @ORM\Table(name="downloadable_file",uniqueConstraints={@ORM\UniqueConstraint(name="unique_file_in_group", columns={"name", "group_id"})})
 */
class DownloadableFile
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $path;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\FileGroup", inversedBy="files")
     * @ORM\JoinColumn(name="group_id", referencedColumnName="id", nullable=false)
     */
    private $group;

    /**
     * @ORM\Column(type="datetime")
     */
    private $uploadTime;


    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Download", mappedBy="file", cascade={"remove"})
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

    public function getPath(): ?string
    {
        return $this->path;
    }

    public function setPath(string $path): self
    {
        $this->path = $path;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getGroup(): ?FileGroup
    {
        return $this->group;
    }

    /**
     * @param mixed $group
     */
    public function setGroup(?FileGroup $group): void
    {
        $this->group = $group;
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
}
