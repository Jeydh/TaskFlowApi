<?php

namespace App\Dto\Project;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Attribute\Groups;

final class ProjectCreateInput
{
    #[Groups(['project:write'])]
    #[Assert\NotBlank]
    public string $name;

    #[Groups(['project:write'])]
    public ?string $description = null;
}