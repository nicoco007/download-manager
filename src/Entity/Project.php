<?php

namespace App\Entity;

use DateInterval;
use DateTime;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\PersistentCollection;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ProjectRepository")
 */
class Project
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
     * @ORM\Column(type="string", length=255, unique=true)
     */
    private $slug;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\DownloadableFile", mappedBy="project", cascade={"remove"})
     * @var PersistentCollection
     */
    private $files;

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
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * @param mixed $slug
     */
    public function setSlug($slug): void
    {
        $this->slug = $slug;
    }

    /**
     * @return mixed
     */
    public function getFiles(): PersistentCollection
    {
        return $this->files;
    }

    public function getDownloadsInPast24Hours(): int {
        $sum = 0;

        $criteria = Criteria::create()->where(Criteria::expr()->gt("time", (new DateTime())->sub(new DateInterval("P1D"))));

        foreach ($this->files as $file) {
            $sum += count($file->getDownloads()->matching($criteria));
        }

        return $sum;
    }

    /**
     * @return int
     */
    public function getTotalDownloads(): int {
        $sum = 0;

        foreach ($this->files as $file) {
            $sum += count($file->getDownloads());
        }

        return $sum;
    }
}