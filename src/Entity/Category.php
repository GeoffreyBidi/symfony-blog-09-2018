<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

// permet de rajouter des annotations pour le controles de contraintes des formulaires
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\CategoryRepository")
 *
 * // validation, contrainte d'unicité sur le nom
 * @UniqueEntity(fields={"name"}, message="Cette catégorie existe déjà.")
 */
class Category
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=20, unique=true)
     *
     * // validation non vide
     * @Assert\NotBlank(message="Le nom est obligatoire")
     * // validation du nombre de caractères
     * @Assert\Length(max="20", maxMessage="Le nom ne doit pas dépasser {{ limit }} caractères.")
     */
    private $name;

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
}
