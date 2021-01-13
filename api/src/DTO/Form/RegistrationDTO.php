<?php

namespace App\DTO\Form;

use App\Validator\Constraints as CustomAssert;
use Symfony\Component\Validator\Constraints as Assert;

class RegistrationDTO
{
    /**
     * @CustomAssert\Unique(class="App\Entity\User", field="email", message="registration.unique_email")
     * @Assert\NotBlank(message="registration.email_not_blank")
     * @Assert\Email(message="registration.email")
     * @Assert\Length(max=180, maxMessage="registration.email_max_length")
     */
    private string $email;

    /**
     * @Assert\NotBlank(message="registration.password_not_blank")
     * @Assert\Length(
     *     min=6,
     *     max=100,
     *     minMessage="registration.password_min_length",
     *     maxMessage="registration.password_max_length"
     * )
     */
    private string $password;

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }
}
