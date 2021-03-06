<?php

namespace App\Entity;

use App\Repository\JobOfferRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=JobOfferRepository::class)
 */
class JobOffer
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $title;

    /**
     * @ORM\ManyToOne(targetEntity=Company::class, inversedBy="jobOffers")
     * @ORM\JoinColumn(nullable=false)
     */
    private $compnay;

    /**
     * @ORM\ManyToMany(targetEntity=Applicant::class, inversedBy="jobOffers")
     */
    private $applicants;

    public function __construct()
    {
        $this->applicants = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getCompnay(): ?Company
    {
        return $this->compnay;
    }

    public function setCompnay(?Company $compnay): self
    {
        $this->compnay = $compnay;

        return $this;
    }

    /**
     * @return Collection|Applicant[]
     */
    public function getApplicants(): Collection
    {
        return $this->applicants;
    }

    public function addApplicant(Applicant $applicant): self
    {
        if (!$this->applicants->contains($applicant)) {
            $this->applicants[] = $applicant;
        }

        return $this;
    }

    public function removeApplicant(Applicant $applicant): self
    {
        $this->applicants->removeElement($applicant);

        return $this;
    }
}
