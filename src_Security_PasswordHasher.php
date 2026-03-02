<?php
/**
 * PasswordHasher - Klasse zur Verwaltung von Passwort-Hashing
 * 
 * Diese Klasse kümmert sich um:
 * - Generierung von zufälligen Salts
 * - Sicheres Hashing von Passwörtern
 * - Verifizierung von Passwörtern
 */

namespace App\Security;

class PasswordHasher
{
    /**
     * Generiert ein zufälliges Salt
     * 
     * Salt ist wichtig, um Regenbogentabellen-Attacken zu verhindern.
     * Jeder Benutzer hat sein eigenes eindeutiges Salt.
     * 
     * @return string Ein zufälliger, hexadezimal enkodierter String
     */
    public static function generateSalt(): string
    {
        // Generiere 32 zufällige Bytes
        $randomBytes = random_bytes(32);
        
        // Konvertiere zu Hexadezimal (für lesbare Speicherung)
        return bin2hex($randomBytes);
    }

    /**
     * Hasht ein Passwort mit einem Salt
     * 
     * Verwendeter Algorithmus: SHA256 mit Salt
     * Formel: hash('sha256', passwort . salt)
     * 
     * @param string $password Das Klartext-Passwort des Benutzers
     * @param string $salt Das eindeutige Salt des Benutzers
     * @return string Der gehashte und gesalzte Passwort-String
     */
    public static function hashPassword(string $password, string $salt): string
    {
        // Kombiniere Passwort mit Salt und hashe das Ergebnis
        return hash('sha256', $password . $salt);
    }

    /**
     * Verifiziert ein Passwort gegen einen Hash
     * 
     * Methode: 
     * 1. Hashe das eingegebene Passwort mit dem gespeicherten Salt
     * 2. Vergleiche das Ergebnis mit dem gespeicherten Hash
     * 
     * @param string $password Das eingegebene Passwort (Klartext)
     * @param string $hash Der gespeicherte Hash aus der Datenbank
     * @param string $salt Das gespeicherte Salt aus der Datenbank
     * @return bool true wenn Passwort korrekt, false sonst
     */
    public static function verifyPassword(string $password, string $hash, string $salt): bool
    {
        // Hashe das eingegebene Passwort mit dem Salt
        $hashedInput = self::hashPassword($password, $salt);
        
        // Vergleiche mit dem gespeicherten Hash (verwende hash_equals für Timing-Attack-Schutz)
        return hash_equals($hash, $hashedInput);
    }
}