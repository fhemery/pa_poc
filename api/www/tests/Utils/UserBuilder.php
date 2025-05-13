<?php

namespace App\Tests\Utils;

/**
 * Builder class for creating test users
 */
class UserBuilder
{
    private ?string $email = null;
    private string $password = 'Test123!';
    private string $firstName = 'Test';
    private string $lastName = 'User';
    
    /**
     * Create a new UserBuilder instance
     */
    public static function create(): self
    {
        return new self();
    }
    
    /**
     * Generate a random user
     */
    public static function Random(): self
    {
        $builder = new self();
        $builder->email = 'test' . uniqid() . '@example.com';
        return $builder;
    }
    
    /**
     * Use the "Alice" test user
     */
    public static function Alice(): TestUser
    {
        $builder = new self();
        $builder->email = 'alice@example.com';
        $builder->password = 'Alice123!';
        $builder->firstName = 'Alice';
        $builder->lastName = 'Wonderland';
        return $builder->build();
    }
    
    /**
     * Use the "Bob" test user
     */
    public static function Bob(): TestUser
    {
        $builder = new self();
        $builder->email = 'bob@example.com';
        $builder->password = 'Bob123!';
        $builder->firstName = 'Bob';
        $builder->lastName = 'Builder';
        return $builder->build();
    }
    
    /**
     * Set the email for the user
     */
    public function withEmail(string $email): self
    {
        $this->email = $email;
        return $this;
    }
    
    /**
     * Set the password for the user
     */
    public function withPassword(string $password): self
    {
        $this->password = $password;
        return $this;
    }
    
    /**
     * Set the first name for the user
     */
    public function withFirstName(string $firstName): self
    {
        $this->firstName = $firstName;
        return $this;
    }
    
    /**
     * Set the last name for the user
     */
    public function withLastName(string $lastName): self
    {
        $this->lastName = $lastName;
        return $this;
    }
    
    /**
     * Build and return a TestUser instance
     */
    public function build(): TestUser
    {
        if ($this->email === null) {
            $this->email = 'test' . uniqid() . '@example.com';
        }
        
        return new TestUser(
            $this->email,
            $this->password,
            $this->firstName,
            $this->lastName
        );
    }
    
    /**
     * Get the email
     */
    public function getEmail(): string
    {
        return $this->email ?? 'test' . uniqid() . '@example.com';
    }
    
    /**
     * Get the password
     */
    public function getPassword(): string
    {
        return $this->password;
    }
}
