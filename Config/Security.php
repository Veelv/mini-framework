<?php
namespace Config;

class Security
{
    /**
     * Hashes a password using the Argon2 algorithm.
     *
     * @param string $password The password to hash.
     * @return string The hashed password.
     */
    public function hashPassword($password)
    {
        $options = [
            'memory_cost' => 1 << 17, // 128MB
            'time_cost' => 4,
            'threads' => 2,
        ];

        return password_hash($password, PASSWORD_ARGON2ID, $options);
    }

    /**
     * Verifies if a password matches a given hash.
     *
     * @param string $password The password to verify.
     * @param string $hash The hashed password.
     * @return bool Returns true if the password matches the hash, false otherwise.
     */
    public function verifyPassword($password, $hashedPassword)
{
    return password_verify($password, $hashedPassword);
}
}