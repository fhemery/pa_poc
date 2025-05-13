<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Service\RefreshTokenService;
use App\Service\TokenBlacklistService;
use App\Service\UserValidatorService;
use Doctrine\ORM\EntityManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/api')]
class UserController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private UserPasswordHasherInterface $passwordHasher,
        private ValidatorInterface $validator,
        private UserRepository $userRepository,
        private JWTTokenManagerInterface $jwtManager,
        private TokenStorageInterface $tokenStorage,
        private TokenBlacklistService $tokenBlacklistService,
        private UserValidatorService $userValidator,
        private RefreshTokenService $refreshTokenService
    ) {
    }

    #[Route('/public/register', name: 'api_register', methods: ['POST'])]
    public function register(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        // Check if user already exists
        $existingUser = $this->userRepository->findOneBy(['email' => $data['email'] ?? '']);
        if ($existingUser) {
            return $this->json([
                'message' => 'User already exists'
            ], Response::HTTP_CONFLICT);
        }

        // Validate registration data
        $validationError = $this->userValidator->validateRegistrationData($data);
        if ($validationError) {
            return $this->json(
                array_diff_key($validationError, ['status' => true]), 
                $validationError['status']
            );
        }

        // Create new user
        $user = new User();
        $user->setEmail($data['email']);
        $user->setFirstName($data['firstName']);
        $user->setLastName($data['lastName']);
        
        // Hash the password
        $hashedPassword = $this->passwordHasher->hashPassword($user, $data['password']);
        $user->setPassword($hashedPassword);
        
        // Set roles (default to ROLE_USER)
        $user->setRoles(['ROLE_USER']);

        // Validate user entity (for any other validation constraints)
        $errors = $this->validator->validate($user);
        if (count($errors) > 0) {
            $errorMessages = [];
            foreach ($errors as $error) {
                $errorMessages[$error->getPropertyPath()] = $error->getMessage();
            }
            
            return $this->json([
                'message' => 'Validation failed',
                'errors' => $errorMessages
            ], Response::HTTP_BAD_REQUEST);
        }

        // Save the user
        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return $this->json([
            'message' => 'User registered successfully',
            'user' => [
                'id' => $user->getId(),
                'email' => $user->getEmail(),
                'firstName' => $user->getFirstName(),
                'lastName' => $user->getLastName()
            ]
        ], Response::HTTP_CREATED);
    }

    #[Route('/login', name: 'api_login', methods: ['POST'])]
    public function login(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        
        $email = $data['email'] ?? null;
        $password = $data['password'] ?? null;
        
        if (empty($email) || empty($password)) {
            return $this->json([
                'message' => 'Email and password are required'
            ], Response::HTTP_BAD_REQUEST);
        }
        
        // Find the user by email
        $user = $this->userRepository->findOneBy(['email' => $email]);
        
        if (!$user) {
            return $this->json([
                'message' => 'Invalid credentials'
            ], Response::HTTP_UNAUTHORIZED);
        }
        
        // Verify the password
        if (!$this->passwordHasher->isPasswordValid($user, $password)) {
            return $this->json([
                'message' => 'Invalid credentials'
            ], Response::HTTP_UNAUTHORIZED);
        }
        
        // Generate JWT access token
        $accessToken = $this->jwtManager->create($user);
        
        // Generate refresh token
        $refreshToken = $this->refreshTokenService->createRefreshToken($user);
        
        return $this->json([
            'accessToken' => $accessToken,
            'refreshToken' => $refreshToken->getToken(),
            'expiresIn' => 3600, // Access token TTL in seconds (1 hour)
            'refreshExpiresIn' => 7776000 // Refresh token TTL in seconds (90 days)
        ]);
    }
    
    #[Route('/users/me', name: 'api_users_me', methods: ['GET'])]
    public function me(): JsonResponse
    {
        /** @var User $user */
        $user = $this->getUser();
        
        if (!$user) {
            return $this->json([
                'message' => 'User not authenticated'
            ], Response::HTTP_UNAUTHORIZED);
        }

        return $this->json([
            'email' => $user->getEmail()
        ]);
    }

    #[Route('/logout', name: 'api_logout', methods: ['POST'])]
    public function logout(Request $request): JsonResponse
    {
        // Get the JWT token from the request
        $authHeader = $request->headers->get('Authorization');
        if (!$authHeader) {
            return $this->json([
                'message' => 'No token provided'
            ], Response::HTTP_BAD_REQUEST);
        }
        
        // Extract the token from the Authorization header
        $token = str_replace('Bearer ', '', $authHeader);
        
        // Get the token payload to determine expiration
        $tokenParts = explode('.', $token);
        if (count($tokenParts) !== 3) {
            return $this->json([
                'message' => 'Invalid token format'
            ], Response::HTTP_BAD_REQUEST);
        }
        
        $payload = json_decode(base64_decode($tokenParts[1]), true);
        if (!isset($payload['exp'])) {
            return $this->json([
                'message' => 'Token has no expiration time'
            ], Response::HTTP_BAD_REQUEST);
        }
        
        // Blacklist the token until its expiration time
        $this->tokenBlacklistService->blacklist($token, $payload['exp']);
        
        // Invalidate the token in the token storage
        $this->tokenStorage->setToken(null);
        
        // Get refresh token from request body
        $data = json_decode($request->getContent(), true);
        if (!empty($data['refreshToken'])) {
            // Revoke the refresh token
            $this->refreshTokenService->revokeRefreshToken($data['refreshToken']);
        }
        
        return $this->json([
            'message' => 'Logged out successfully'
        ]);
    }
    
    #[Route('/refresh-token', name: 'api_refresh_token', methods: ['POST'])]
    public function refreshToken(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        
        if (empty($data['refreshToken'])) {
            return $this->json([
                'message' => 'Refresh token is required'
            ], Response::HTTP_BAD_REQUEST);
        }
        
        try {
            // Validate the refresh token
            $refreshToken = $this->refreshTokenService->validateRefreshToken($data['refreshToken']);
            
            // Get the user from the refresh token
            $user = $refreshToken->getUser();
            
            // Generate a new access token
            $accessToken = $this->jwtManager->create($user);
            
            // Optionally, rotate the refresh token for better security
            // This invalidates the old refresh token and creates a new one
            $this->refreshTokenService->revokeRefreshToken($data['refreshToken']);
            $newRefreshToken = $this->refreshTokenService->createRefreshToken($user);
            
            return $this->json([
                'accessToken' => $accessToken,
                'refreshToken' => $newRefreshToken->getToken(),
                'expiresIn' => 3600, // Access token TTL in seconds (1 hour)
                'refreshExpiresIn' => 7776000 // Refresh token TTL in seconds (90 days)
            ]);
        } catch (\Exception $e) {
            return $this->json([
                'message' => $e->getMessage()
            ], Response::HTTP_UNAUTHORIZED);
        }
    }
}
