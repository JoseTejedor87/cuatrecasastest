<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Model as ORMBehaviors;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Entity(repositoryClass="App\Repository\LawyerRepository")
 */
class Lawyer extends Publishable
{
    use ORMBehaviors\Translatable\Translatable;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $surname;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $initials;
    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=128, nullable=true)
     */
    private $phone;

    /**
     * @ORM\Column(type="string", length=128, nullable=true)
     */
    private $fax;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $lawyerType;

    /**
     * @Gedmo\Slug(fields={"name", "surname"}, updatable=false)
     * @ORM\Column(length=128, unique=true)
     */
    private $slug;


    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Activity", inversedBy="lawyers")
     */
    private $activities;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Activity", inversedBy="lawyers_secondary")
     * @ORM\JoinTable(name="lawyer_secondary_activity",
     *      joinColumns = {@ORM\JoinColumn(name="lawyer_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="activity_id", referencedColumnName="id")}
     * )
     */
    private $secondaryActivities;


    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Activity", mappedBy="key_contacts")
     */
    private $specificActivities;

    /**
     * @ORM\Column(type="json", nullable=true)
     */
    private $knownLanguages = [];

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Resource", mappedBy="lawyer", cascade={"persist"}, orphanRemoval=true)
     */
    private $photo;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Person", mappedBy="lawyer", orphanRemoval=true)
     */
    private $person;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Office", inversedBy="lawyer")
     * @ORM\JoinColumn(nullable=true)
     */
    private $office;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Insight", mappedBy="lawyers")
     */
    private $insights;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\CaseStudy", mappedBy="lawyers")
     */
    private $caseStudies;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Training", mappedBy="lawyer" , cascade={"persist"}, orphanRemoval=true)
     */
    private $trainings;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Mention", mappedBy="lawyer" , cascade={"persist"}, orphanRemoval=true)
     */
    private $mentions;

    public function __construct()
    {
        $this->activities = new ArrayCollection();
        $this->secondaryActivities = new ArrayCollection();
        $this->insights = new ArrayCollection();
        $this->caseStudies = new ArrayCollection();
        $this->mentions = new ArrayCollection();
        $this->trainings = new ArrayCollection();
        $this->specificActivities = new ArrayCollection();
    }

    public function __toString()
    {
        return $this->getFullName();
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getSurname(): ?string
    {
        return $this->surname;
    }

    public function setSurname(?string $surname): self
    {
        $this->surname = $surname;

        return $this;
    }

    public function getFullName(): ?string
    {
        return $this->name . " " . $this->surname;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(?string $phone): self
    {
        $this->phone = $phone;

        return $this;
    }

    public function getFax(): ?string
    {
        return $this->fax;
    }

    public function setFax(?string $fax): self
    {
        $this->fax = $fax;

        return $this;
    }

    public function getLawyerType(): ?string
    {
        return $this->lawyerType;
    }

    public function setLawyerType(?string $lawyerType): self
    {
        $this->lawyerType = $lawyerType;

        return $this;
    }

    public function getSlug()
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }


    /**
     * @return Collection|Activity[]
     */
    public function getActivities(): Collection
    {
        return $this->activities;
    }

    public function addActivity(Activity $activity): self
    {
        if (!$this->activities->contains($activity)) {
            $this->activities[] = $activity;
        }

        return $this;
    }

    public function removeActivity(Activity $activity): self
    {
        if ($this->activities->contains($activity)) {
            $this->activities->removeElement($activity);
        }

        return $this;
    }

    public function getPhoto(): ?Resource
    {
        return $this->photo;
    }

    public function setPhoto(?Resource $photo): self
    {
        $this->photo = $photo;
        if ($photo) {
            $photo->setLawyer($this);
        }

        return $this;
    }
    public function getOffice(): ?Office
    {
        return $this->office;
    }

    public function setOffice(?Office $office): self
    {
        $this->office = $office;

        return $this;
    }

    public function getKnownLanguages(): ?array
    {
        return $this->knownLanguages;
    }

    public function setKnownLanguages(array $languages): self
    {
        $this->knownLanguages = $languages;
        return $this;
    }

    /**
     * @return Collection|Activity[]
     */
    public function getSecondaryActivities(): Collection
    {
        return $this->secondaryActivities;
    }

    public function addSecondaryActivity(Activity $secondaryActivity): self
    {
        if (!$this->secondaryActivities->contains($secondaryActivity)) {
            $this->secondaryActivities[] = $secondaryActivity;
        }

        return $this;
    }

    public function removeSecondaryActivity(Activity $secondaryActivity): self
    {
        if ($this->secondaryActivities->contains($secondaryActivity)) {
            $this->secondaryActivities->removeElement($secondaryActivity);
        }

        return $this;
    }


    public function getPerson(): ?Person
    {
        return $this->person;
    }

    public function setPerson(?Person $person): self
    {
        $this->person = $person;

        // set (or unset) the owning side of the relation if necessary
        $newLawyer = null === $person ? null : $this;
        if ($person->getLawyer() !== $newLawyer) {
            $person->setLawyer($newLawyer);
        }

        return $this;
    }

    /**
     * @return Collection|Insight[]
     */
    public function getInsights(): Collection
    {
        return $this->insights;
    }

    public function addInsight(Insight $insight): self
    {
        if (!$this->insights->contains($insight)) {
            $this->insights[] = $insight;
            $insight->addLawyer($this);
        }

        return $this;
    }

    public function removeInsight(Insight $insight): self
    {
        if ($this->insights->contains($insight)) {
            $this->insights->removeElement($insight);
            $insight->removeLawyer($this);
        }

        return $this;
    }

    /**
     * @return Collection|CaseStudy[]
     */
    public function getCaseStudies(): Collection
    {
        return $this->caseStudies;
    }

    public function addCaseStudy(CaseStudy $caseStudy): self
    {
        if (!$this->caseStudies->contains($caseStudy)) {
            $this->caseStudies[] = $caseStudy;
            $caseStudy->addLawyer($this);
        }

        return $this;
    }

    public function removeCaseStudy(CaseStudy $caseStudy): self
    {
        if ($this->caseStudies->contains($caseStudy)) {
            $this->caseStudies->removeElement($caseStudy);
            $caseStudy->removeLawyer($this);
        }

        return $this;
    }

    /**
     * @return Collection|Mention[]
     */
    public function getMentions(): Collection
    {
        return $this->mentions;
    }

    public function addMention(Mention $mention): self
    {
        if (!$this->mentions->contains($mention)) {
            $this->mentions[] = $mention;
            $mention->setLawyer($this);
        }

        return $this;
    }

    public function removeMention(Mention $mention): self
    {
        if ($this->mentions->contains($mention)) {
            $this->mentions->removeElement($mention);
            // set the owning side to null (unless already changed)
            if ($mention->getLawyer() === $this) {
                $mention->setLawyer(null);
            }
        }

        return $this;
    }

    /**
         * @return Collection|Training[]
         */
    public function getTrainings(): Collection
    {
        return $this->trainings;
    }

    public function addTraining(Training $training): self
    {
        if (!$this->trainings->contains($training)) {
            $this->trainings[] = $training;
            $training->setLawyer($this);
        }

        return $this;
    }

    public function removeTraining(Training $training): self
    {
        if ($this->trainings->contains($training)) {
            $this->trainings->removeElement($training);
            // set the owning side to null (unless already changed)
            if ($training->getLawyer() === $this) {
                $training->setLawyer(null);
            }
        }

        return $this;
    }

    public function getInitials(): ?string
    {
        return $this->initials;
    }

    public function setInitials(?string $initials): self
    {
        $this->initials = $initials;

        return $this;
    }

    /**
     * @return Collection|Activity[]
     */
    public function getSpecificActivities(): Collection
    {
        return $this->specificActivities;
    }

    public function addSpecificActivity(Activity $specificActivity): self
    {
        if (!$this->specificActivities->contains($specificActivity)) {
            $this->specificActivities[] = $specificActivity;
            $specificActivity->addKeyContact($this);
        }

        return $this;
    }

    public function removeSpecificActivity(Activity $specificActivity): self
    {
        if ($this->specificActivities->contains($specificActivity)) {
            $this->specificActivities->removeElement($specificActivity);
            $specificActivity->removeKeyContact($this);
        }

        return $this;
    }








}
