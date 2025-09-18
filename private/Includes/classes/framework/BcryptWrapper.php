<?php
namespace App\Security;
use InvalidArgumentException;

/**
 * BcryptWrapper.php
 *
 * Class for creating bcrypt-hashed passwords and validating stored hashes.
 *
 * Notes:
 * - Authored primarily by Clinton Ingram.
 * - Template and usage based on CF Ingrams’ Web Application Development lecture materials.
 * - Logic reused from previous project: CryptoShow.
 *
 * Responsibilities:
 * - Hash user passwords securely using bcrypt
 * - Validate user-provided passwords against stored hashes
 * - Check if a stored hash needs rehashing
 *
 * @author: Afnan Khalid
 * References: 
 *   - CF Ingrams, De Montfort University (cfi@dmu.ac.uk) – Web Application Development (CTEC2712_2023_603)
 *   - Previous project: CryptoShow – reused structure
 * @package: Royal Silk Leicester
 */

class BcryptWrapper
{
    // Default cost for bcrypt hashing (adjust as needed)
    private const DEFAULT_COST = 10;

    /**
     * Hash a user password using bcrypt.
     *
     * @param string $validated_user_password The plain-text password to hash.
     * @param int $cost Optional cost parameter for bcrypt.
     * @return string The hashed password.
     * @throws InvalidArgumentException If the password is empty.
     */
    public static function hashPassword(string $validated_user_password, int $cost = self::DEFAULT_COST): string
    {
        if (empty($validated_user_password)) {
            throw new InvalidArgumentException("Password cannot be empty.");
        }

        $bcrypt_options = ['cost' => $cost];

        return password_hash($validated_user_password, PASSWORD_DEFAULT, $bcrypt_options);
    }

    /**
     * Validate a plain-text password against a stored hash.
     *
     * @param string $current_user_password The plain-text password to verify.
     * @param string $stored_user_password_hash The stored bcrypt hash.
     * @return bool True if password matches, false otherwise.
     */
    public static function validatePassword(string $current_user_password, string $stored_user_password_hash): bool
    {
        return password_verify($current_user_password, $stored_user_password_hash);
    }

    /**
     * Check if the stored hash needs rehashing (e.g., if cost parameter has changed).
     *
     * @param string $stored_user_password_hash The stored bcrypt hash.
     * @param int $cost Optional cost parameter for bcrypt.
     * @return bool True if rehash is needed, false otherwise.
     */
    public static function needsRehash(string $stored_user_password_hash, int $cost = self::DEFAULT_COST): bool
    {
        return password_needs_rehash($stored_user_password_hash, PASSWORD_DEFAULT, ['cost' => $cost]);
    }
}