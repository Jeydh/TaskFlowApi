<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Dto\Project\ProjectCreateInput;
use App\Entity\Project;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use App\Entity\User;

/**
 * @implements ProcessorInterface<ProjectCreateInput, Project>
 */
final class ProjectCreateProcessor implements ProcessorInterface
{
    public function __construct(
        #[Autowire(service: 'api_platform.doctrine.orm.state.persist_processor')]
        private ProcessorInterface $persistProcessor,
        private Security $security,
    ) 
    {}

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): object
    {
        // @phpstan-ignore-next-line Defensive runtime guard: only process ProjectCreateInput DTOs, delegate others
        if (!$data instanceof ProjectCreateInput) {
            throw new \InvalidArgumentException('Expected ProjectCreateInput.');
        }

        /** @var User $user */
        $owner = $this->security->getUser();
        $now = new \DateTimeImmutable();

        $project = new Project();
        $project->setName($data->name);
        $project->setDescription($data->description ?? null);
        $project->setOwner($owner);
        $project->addMember($owner);
        $project->setCreatedAt($now);
        $project->setUpdatedAt($now);
        
        return $this->persistProcessor->process($project, $operation, $uriVariables, $context);
    }
}