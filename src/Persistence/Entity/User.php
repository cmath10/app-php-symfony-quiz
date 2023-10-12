<?php

namespace App\Persistence\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Mapping\ClassMetadata;

#[ORM\Table(name: 't_users')]
#[ORM\Entity]
#[UniqueEntity(fields: ['nickname'], message: 'There is already an account with this nickname')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Column(type: Types::INTEGER)]
    #[ORM\Id, ORM\GeneratedValue(strategy: 'AUTO')]
    private ?int $id = null;

    #[ORM\Column(type: Types::STRING)]
    private string $nickname = '';

    #[ORM\Column(type: Types::STRING)]
    private string $password = '';

    /** @noinspection PhpUnused */
    public static function loadValidatorMetadata(ClassMetadata $metadata): void
    {
        $metadata
            ->addPropertyConstraints('nickname', [
                new Assert\NotBlank(),
                new Assert\Length(
                    min: 3,
                    minMessage: 'Nickname should be at least {{ limit }} characters'
                ),
            ])
//            ->addPropertyConstraint('password', new Assert\NotBlank())
        ;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNickName(): string
    {
        return $this->nickname;
    }

    public function setNickname(string $nickname): User
    {
        $this->nickname = $nickname;
        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): User
    {
        $this->password = $password;
        return $this;
    }

    public function getUserIdentifier(): string
    {
        return $this->nickname;
    }

    public function getRoles(): array
    {
        return [];
    }

    public function eraseCredentials()
    {
    }
}