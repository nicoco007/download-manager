<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\PersistentCollection;

/**
 * @ORM\Entity(repositoryClass="App\Repository\FileGroupRepository")
 */
class Folder
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
     * @ORM\OneToMany(targetEntity="App\Entity\DownloadableFile", mappedBy="folder", cascade={"remove"})
     * @var PersistentCollection
     */
    private $files;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Folder", mappedBy="parent", cascade={"remove"})
     * @var PersistentCollection
     */
    private $folders;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Folder", inversedBy="folders")
     * @var Folder
     */
    private $parent;

    public function getId(): int
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

    /**
     * @return mixed
     */
    public function getFiles(): PersistentCollection
    {
        return $this->files;
    }

    /**
     * @return mixed
     */
    public function getParent(): ?Folder
    {
        return $this->parent;
    }

    /**
     * @param mixed $parent
     */
    public function setParent($parent): void
    {
        $this->parent = $parent;
    }

    /**
     * @return mixed
     */
    public function getFolders(): PersistentCollection
    {
        return $this->folders;
    }

    public function getPath(): string
    {
        $folder = $this;
        $path = $folder->getName();

        while (($parent = $folder->getParent()) !== null) {
            $folder = $parent;
            $path = $parent->getName() . '/' . $path;
        }

        return $path;
    }
}