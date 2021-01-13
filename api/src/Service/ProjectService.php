<?php

namespace App\Service;

use App\DTO\Form\CreateProjectDTO;
use App\DTO\ProjectDTO;
use App\Entity\Project;
use App\Entity\User;
use App\Exception\FormValidationException;
use App\Factory\DTO\ProjectDTOFactory;
use App\Factory\ProjectFactory;
use App\Form\CreateProjectType;
use App\Utils\FormErrors;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Security\Core\Security;

class ProjectService
{

    private ProjectFactory $projectFactory;

    private FormFactoryInterface $formFactory;

    private EntityManagerInterface $em;

    private ProjectDTOFactory $projectDTOFactory;

    private Security $security;

    public function __construct(
        ProjectFactory $projectFactory,
        FormFactoryInterface $formFactory,
        EntityManagerInterface $em,
        ProjectDTOFactory $projectDTOFactory,
        Security $security
    ) {
        $this->projectFactory = $projectFactory;
        $this->formFactory = $formFactory;
        $this->em = $em;
        $this->projectDTOFactory = $projectDTOFactory;
        $this->security = $security;
    }

    /**
     * @throws FormValidationException
     */
    public function create(array $payload): ProjectDTO
    {
        $createProjectDTO = new CreateProjectDTO();
        $form = $this->formFactory->create(CreateProjectType::class, $createProjectDTO);
        $form->submit($payload);

        if ($form->isValid()) {
            $project = $this->projectFactory->create($createProjectDTO->name);
            $this->em->persist($project);
            $this->em->flush();

            return $this->projectDTOFactory->create($project);
        } else {
            throw new FormValidationException(FormErrors::getAll($form));
        }
    }

    /**
     * @return ProjectDTO[]
     */
    public function getLoggedUserProjects(): array
    {
        /** @var User $user */
        $user = $this->security->getUser();
        $projects = [];
        foreach ($user->getProjects() as $project) {
            $projects[] = $this->projectDTOFactory->create($project);
        }

        return $projects;
    }
}
