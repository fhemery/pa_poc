<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class UserValidatorService
{
    /**
     * Validate user registration data
     * 
     * @param array $data User registration data
     * @return array|null Returns null if validation passes, or an array with error details if validation fails
     */
    public function validateRegistrationData(array $data): ?array
    {
        // Validate required fields
        if (!isset($data['email']) || !isset($data['password']) || !isset($data['firstName']) || !isset($data['lastName'])) {
            return [
                'message' => 'Missing required fields',
                'status' => Response::HTTP_BAD_REQUEST
            ];
        }

        // Validate email format
        if (empty($data['email'])) {
            return [
                'message' => 'Email cannot be empty',
                'field' => 'email',
                'status' => Response::HTTP_BAD_REQUEST
            ];
        }
        
        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            return [
                'message' => 'Invalid email format',
                'field' => 'email',
                'status' => Response::HTTP_BAD_REQUEST
            ];
        }
        
        // Validate password
        if (empty($data['password'])) {
            return [
                'message' => 'Password cannot be empty',
                'field' => 'password',
                'status' => Response::HTTP_BAD_REQUEST
            ];
        }
        
        if (strlen($data['password']) < 6) {
            return [
                'message' => 'Password must be at least 6 characters long',
                'field' => 'password',
                'status' => Response::HTTP_BAD_REQUEST
            ];
        }

        // All validations passed
        return null;
    }
}
