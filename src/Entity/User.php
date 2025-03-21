<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Validator\Constraints as Assert;


#[ORM\Entity(repositoryClass: UserRepository::class)]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Column(type: "string", nullable: true)]
    private $password;

    #[ORM\Column(type: Types::JSON)]
    private array $roles = [];


    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nom = null;

    #[ORM\Column(length: 255)]
    private ?string $prenom = null;

    #[ORM\Column(length: 255)]
    private ?string $telephone = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $datedenaissance = null;

    #[ORM\Column(length: 255)]
    private ?string $email = null;

    #[ORM\Column(length: 255)]
    private ?string $adresse = null;

    #[ORM\Column(length: 255)]
    private ?string $profession = null;

    #[ORM\Column]
    private ?bool $client = null;

    #[ORM\Column(nullable: true)]
    private ?int $recommandation_id = null;

    #[ORM\ManyToOne(targetEntity: self::class, inversedBy: 'users')]
    #[ORM\JoinColumn(name: "recommandation_id", referencedColumnName: "id")]
    private ?self $recommandation = null;

    #[ORM\OneToMany(mappedBy: 'recommandation', targetEntity: self::class)]
    private Collection $users;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $commentaire = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $contratsSouscrits = null;

    #[ORM\Column(nullable: true)]
    private ?int $Enfants = null;

    #[ORM\Column(length: 255)]
    private ?string $Civil = null;

    public function __construct()
    {
        $this->users = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(string $prenom): self
    {
        $this->prenom = $prenom;

        return $this;
    }

    public function getTelephone(): ?string
    {
        return $this->telephone;
    }

    public function setTelephone(string $telephone): self
    {
        $this->telephone = $telephone;

        return $this;
    }

    public function getDatedenaissance(): ?\DateTimeInterface
    {
        return $this->datedenaissance;
    }

    public function setDatedenaissance(\DateTimeInterface $datedenaissance): self
    {
        $this->datedenaissance = $datedenaissance;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getAdresse(): ?string
    {
        return $this->adresse;
    }

    public function setAdresse(string $adresse): self
    {
        $this->adresse = $adresse;

        return $this;
    }

    public function getProfession(): ?string
    {
        return $this->profession;
    }

    public function setProfession(string $profession): self
    {
        $this->profession = $profession;

        return $this;
    }

    public function isClient(): ?bool
    {
        return $this->client;
    }

    public function setClient(bool $client): self
    {
        $this->client = $client;

        return $this;
    }

    public function getRecommandation(): ?self
    {
        return $this->recommandation;
    }

    public function setRecommandation(?self $recommandation): self
    {
        $this->recommandation = $recommandation;

        return $this;
    }

    /**
     * @return Collection<int, self>
     */
    public function getUsers(): Collection
    {
        return $this->users;
    }

    public function addUser(self $user): self
    {
        if (!$this->users->contains($user)) {
            $this->users->add($user);
            $user->setRecommandation($this);
        }

        return $this;
    }

    public function removeUser(self $user): self
    {
        if ($this->users->removeElement($user)) {
            // set the owning side to null (unless already changed)
            if ($user->getRecommandation() === $this) {
                $user->setRecommandation(null);
            }
        }

        return $this;
    }

    public function getCommentaire(): ?string
    {
        return $this->commentaire;
    }

    public function setCommentaire(?string $commentaire): self
    {
        $this->commentaire = $commentaire;

        return $this;
    }

    public function getContratsSouscrits(): ?string
    {
        return $this->contratsSouscrits;
    }

    public function setContratsSouscrits(?string $contratsSouscrits): self
    {
        $this->contratsSouscrits = $contratsSouscrits;

        return $this;
    }

    public function getPassword(): string
    {
        return (string) $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getSalt()
    {
        // not needed when using the "bcrypt" algorithm in security.yaml
    }

    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    public function getRoles(): array
    {
        $roles = $this->roles ?? [];
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        if ($roles === null) {
            $this->roles = [];
        } else {
            $this->roles = $roles;
        }

        return $this;
    }

    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
    }

    public function __toString(): string
    {
        return $this->getNom() . ' ' . $this->getPrenom();
    }

    public function getEnfants(): ?int
    {
        return $this->Enfants;
    }

    public function setEnfants(?int $Enfants): self
    {
        $this->Enfants = $Enfants;

        return $this;
    }

    public function getCivil(): ?string
    {
        return $this->Civil;
    }

    public function setCivil(string $Civil): self
    {
        $this->Civil = $Civil;

        return $this;
    }
}
