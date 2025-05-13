<?php

namespace App\Tests\Utils;

/**
 * Represents a test user for use in tests
 */
class TestUser
{
    private string $email;
    private string $password;
    private string $firstName;
    private string $lastName;
    
    /**
     * Create a new TestUser instance
     */
    public function __construct(string $email, string $password, string $firstName, string $lastName)
    {
        $this->email = $email;
        $this->password = $password;
        $this->firstName = $firstName;
        $this->lastName = $lastName;
    }
    
    /**
     * Get the email address
     */
    public function getEmail(): string
    {
        return $this->email;
    }
    
    /**
     * Get the password
     */
    public function getPassword(): string
    {
        return $this->password;
    }
    
    /**
     * Get the first name
     */
    public function getFirstName(): string
    {
        return $this->firstName;
    }
    
    /**
     * Get the last name
     */
    public function getLastName(): string
    {
        return $this->lastName;
    }
    
    /**
     * Get the full name (first name + last name)
     */
    public function getFullName(): string
    {
        return $this->firstName . ' ' . $this->lastName;
    }
    
    /**
     * Convert to an array for registration
     */
    public function toRegistrationArray(): array
    {
        return [
            'email' => $this->email,
            'password' => $this->password,
            'firstName' => $this->firstName,
            'lastName' => $this->lastName
        ];
    }
    
    /**
     * Convert to an array for login
     */
    public function toLoginArray(): array
    {
        return [
            'username' => $this->email,
            'password' => $this->password
        ];
    }
}
