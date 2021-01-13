<?php

namespace App\Service;

use App\DTO\Form\RegistrationDTO;
use App\DTO\UserDTO;
use App\Exception\FormValidationException;
use App\Factory\UserFactory;
use App\Form\RegistrationType;
use App\Utils\FormErrors;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormFactoryInterface;

class UserService
{
    private FormFactoryInterface $formFactory;

    private UserFactory $userFactory;

    private EntityManagerInterface$em;

    public function __construct(
        FormFactoryInterface $formFactory,
        UserFactory $userFactory,
        EntityManagerInterface $em
    ) {
        $this->formFactory = $formFactory;
        $this->userFactory = $userFactory;
        $this->em = $em;
    }

    /**
     * @throws FormValidationException
     */
    public function register(array $payload): UserDTO
    {
        $registrationDTO = new RegistrationDTO();
        $form = $this->formFactory->create(RegistrationType::class, $registrationDTO);
        $form->submit($payload);

        if ($form->isValid()) {
            $user = $this->userFactory->create($registrationDTO->getEmail(), $registrationDTO->getPassword());
            $this->em->persist($user);
            $this->em->flush();

            return new UserDTO($user);
        } else {
            throw new FormValidationException(FormErrors::getAll($form));
        }
    }
}
