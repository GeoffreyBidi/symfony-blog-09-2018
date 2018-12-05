<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\CommentRepository")
 */
class Comment
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="text", length=200)
     * @Assert\NotBlank(message="Veuillez saisir du texte")
     */
    private $content;

    /**
     * @ORM\Column(type="datetime")
     *
     */
    private $publicationDateComment;

    /**
     *  @var User
     *  @ORM\JoinColumn(nullable=false)
     *  @ORM\ManyToOne(targetEntity="User", inversedBy="comment")
     */
    private $user;

    /**
     *  @var Article
     *  @ORM\JoinColumn(nullable=false)
     *  @ORM\ManyToOne(targetEntity="Article", inversedBy="comment")
     */
    private $article;

    public function __construct()
    {
        $this->setPublicationDateComment(new \DateTime());
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;

        return $this;
    }

    public function getPublicationDateComment(): ?\DateTimeInterface
    {
        return $this->publicationDateComment;
    }

    public function setPublicationDateComment(\DateTimeInterface $publicationDateComment): self
    {
        $this->publicationDateComment = $publicationDateComment;

        return $this;
    }

    /**
     * @return User
     */
    public function getUser(): ?User
    {
        return $this->user;
    }

    /**
     * @param User $user
     * @return Comment
     */
    public function setUser(User $user): Comment
    {
        $this->user = $user;
        return $this;
    }

    /**
     * @return Article
     */
    public function getArticle(): ?Article
    {
        return $this->article;
    }

    /**
     * @param Article $article
     * @return Comment
     */
    public function setArticle(Article $article): Comment
    {
        $this->article = $article;
        return $this;
    }

    public function __toString()
    {
        return $this->content. ' ' . $this->user;
    }
}
