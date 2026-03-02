<?php
/**
 * User Entity - Repräsentiert einen Benutzer in der Datenbank
 * 
 * Diese Klasse definiert die Struktur der Benutzertabelle und ihre Eigenschaften.
 * Mit Doctrine ORM werden diese Properties automatisch in Datenbankspalten gemappt.
 */

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="users")
 * 
 * Diese Annotations sagen Doctrine, dass diese Klasse zu einer Tabelle "users" in der DB gemappt wird
 */
class User
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * 
     * Die ID ist der Primärschlüssel und wird automatisch inkrementiert
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=100, unique=true)
     * 
     * Benutzername: Eindeutig, max 100 Zeichen
     */
    private $username;

    /**
     * @ORM\Column(type="string", length=255)
     * 
     * E-Mail-Adresse: Kann länger sein wegen Domain-Namen
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=255)
     * 
     * Gesalzenes Passwort-Hash: Speichert den gehashten Wert
     */
    private $password;

    /**
     * @ORM\Column(type="string", length=255)
     * 
     * Salt: Zufälliger Wert zur Erhöhung der Sicherheit des Passwort-Hashes
     */
    private $salt;

    /**
     * @ORM\Column(type="boolean")
     * 
     * Aktivierungsstatus: false = inaktiv (Standard), true = aktiv
     */
    private $isActive = false;

    /**
     * @ORM\Column(type="boolean")
     * 
     * Admin-Rechte: false = normaler Benutzer (Standard), true = Administrator
     */
    private $isAdmin = false;

    /**
     * @ORM\Column(type="datetime_immutable", options={"default":"CURRENT_TIMESTAMP"})
     * 
     * Erstellungszeitpunkt: Wann wurde das Konto registriert
     */
    private $createdAt;

    // ============== GETTER und SETTER ==============

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;
        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;
        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;
        return $this;
    }

    public function getSalt(): ?string
    {
        return $this->salt;
    }

    public function setSalt(string $salt): self
    {
        $this->salt = $salt;
        return $this;
    }

    public function isActive(): ?bool
    {
        return $this->isActive;
    }

    public function setIsActive(bool $isActive): self
    {
        $this->isActive = $isActive;
        return $this;
    }

    public function isAdmin(): ?bool
    {
        return $this->isAdmin;
    }

    public function setIsAdmin(bool $isAdmin): self
    {
        $this->isAdmin = $isAdmin;
        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): self
    {
        $this->createdAt = $createdAt;
        return $this;
    }
}