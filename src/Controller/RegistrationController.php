<?php
// src/Controller/Api/RegistrationController.php
namespace App\Controller;

use App\Entity\Organization;
use App\Entity\User;
use App\Manager\UserManager;
use Doctrine\ORM\EntityManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class RegistrationController extends AbstractController
{
    #[Route('/register', name: 'api_register', methods: ['POST'])]
    public function register(
        Request                     $request,
        EntityManagerInterface      $em,
        UserPasswordHasherInterface $passwordHasher,
        UserManager                 $userManager
    ): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $email = $data['email'] ?? null;
        $password = $data['password'] ?? null;
        $name = $data['name'] ?? null;
        $organizationName = $data['organization_name'] ?? null;
        $subdomain = $data['subdomain'] ?? null;

        if (!$email || !$password || !$name || !$organizationName || !$subdomain) {
            return new JsonResponse(['error' => 'Missing parameters'], JsonResponse::HTTP_BAD_REQUEST);
        }

        // Проверяем, существует ли пользователь с таким email
        $existingUser = $em->getRepository(User::class)->findOneBy(['email' => $email]);
        $existingOrganization = $em->getRepository(Organization::class)->findOneBy(['subdomain' => $email]);
        if ($existingUser) {
            return new JsonResponse(['error' => 'User already exists'], JsonResponse::HTTP_BAD_REQUEST);
        }
        if ($existingOrganization) {
            return new JsonResponse(['error' => 'Domain already exists'], JsonResponse::HTTP_BAD_REQUEST);
        }

        // Создаем нового пользователя
        $organization = new Organization();
        $organization->setName($organizationName);
        $organization->setSubdomain($subdomain);
        $em->persist($organization);
        $user = new User();
        $user->setEmail($email);
        $user->setName($email);

        // Хешируем пароль с помощью нового хешера
        $hashedPassword = $passwordHasher->hashPassword($user, $password);
        $user->setPassword($hashedPassword);

        // Сохраняем пользователя в базу данных
        $em->persist($user);
        $userManager->attachToOrganization($user, $organization);
        $em->flush();

        return new JsonResponse(
            ['message' =>
                'User successfully registered',
                'data' =>
                    [
                        'user' => ['id' => $user->getId(), 'email' => $user->getEmail()],
                        'organization' => ['id' => $organization->getId(), 'name' => $organization->getName(), 'subdomain' => $organization->getSubdomain()]
                    ]

            ], Response::HTTP_CREATED);
    }

    #[Route('/auth', name: 'api_login', methods: ['POST'])]
    public function login(
        Request                     $request,
        UserPasswordHasherInterface $passwordHasher,
        JWTTokenManagerInterface    $JWTManager,
        EntityManagerInterface      $em
    ): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $email = $data['email'] ?? null;
        $password = $data['password'] ?? null;

        if (!$email || !$password) {
            return new JsonResponse(['error' => 'Missing email or password'], JsonResponse::HTTP_BAD_REQUEST);
        }

        // Проверка наличия пользователя в базе данных
        $user = $em->getRepository(User::class)->findOneBy(['email' => $email]);
        if (!$user) {
            return new JsonResponse(['error' => 'User not found'], JsonResponse::HTTP_NOT_FOUND);
        }

        // Проверяем, совпадает ли пароль
        if (!$passwordHasher->isPasswordValid($user, $password)) {
            return new JsonResponse(['error' => 'Invalid password'], JsonResponse::HTTP_UNAUTHORIZED);
        }

        // Генерация JWT
        $token = $JWTManager->create($user);

        return new JsonResponse(['token' => $token]);
    }
}
