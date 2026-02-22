<?php

namespace App\Controller\Api\Auth;

use App\Dto\Auth\RegisterUserInput;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\Exception\ExceptionInterface as SerializerExceptionInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class RegisterController extends AbstractController
{
    #[Route('/api/register', name: 'api_register', methods: ['POST'])]
    public function __invoke(
        Request $request,
        SerializerInterface $serializer,
        ValidatorInterface $validator,
        UserPasswordHasherInterface $passwordHasher,
        EntityManagerInterface $entityManager,
    ): JsonResponse {
        try {
            /** @var RegisterUserInput $input */
            $input = $serializer->deserialize($request->getContent(), RegisterUserInput::class, 'json');
        } catch (SerializerExceptionInterface) {
            return $this->json([
                'message' => 'Invalid JSON payload.',
            ], 400);
        }

        $errors = $validator->validate($input);

        if (count($errors) > 0) {
            $formattedErrors = [];

            foreach ($errors as $error) {
                $formattedErrors[] = [
                    'field' => $error->getPropertyPath(),
                    'message' => $error->getMessage(),
                ];
            }

            return $this->json([
                'message' => 'Validation failed.',
                'errors' => $formattedErrors,
            ], 422);
        }

        $existingUser = $entityManager->getRepository(User::class)->findOneBy([
            'email' => $input->email,
        ]);

        if ($existingUser !== null) {
            return $this->json([
                'message' => 'Email already exists.',
            ], 409);
        }

        $user = new User();
        $user->setEmail($input->email);
        $user->setRoles(['ROLE_USER']);
        $user->setPassword($passwordHasher->hashPassword($user, $input->password));

        $entityManager->persist($user);
        $entityManager->flush();

        return $this->json([
            'id' => $user->getId(),
            'email' => $user->getUserIdentifier(),
            'roles' => $user->getRoles(),
        ], 201);
    }
}