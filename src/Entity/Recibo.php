<?php

namespace App\Entity;

use App\Repository\ReciboRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints\DateTime;

/**
 * @ORM\Entity(repositoryClass=ReciboRepository::class)
 */
class Recibo
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $legajo;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $usuario;

    /**
     * @ORM\Column(type="datetime")
     */
    private $fechaCarga;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $pathArchive;

    /**
     * @ORM\Column(type="string", length=20, nullable=true)
     */
    private $estado;

    /**
     * @ORM\Column(type="string", length=4)
     */
    private $anio;

    /**
     * @ORM\Column(type="string", length=15)
     */
    private $mes;

    public function __construct($legajo, $usuario, $fechaCarga, $pathArchive, $anio,$mes, $estado)
    {
        $this->setLegajo($legajo);
        $this->setUsuario($usuario);
        $this->setFechaCarga($fechaCarga);
        $this->setPathArchive($pathArchive);
        $this->setEstado($estado);
        $this->setAnio($anio);
        $this->setMes($mes);
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLegajo(): ?string
    {
        return $this->legajo;
    }

    public function setLegajo(string $legajo): self
    {
        $this->legajo = $legajo;

        return $this;
    }

    public function getUsuario(): ?string
    {
        return $this->usuario;
    }

    public function setUsuario(string $usuario): self
    {
        $this->usuario = $usuario;

        return $this;
    }

    public function getFechaCarga(): ?\DateTime
    {
        return $this->fechaCarga;
    }

    public function setFechaCarga(\DateTime $fechaCarga): self
    {
        $this->fechaCarga = $fechaCarga;

        return $this;
    }

    public function getPathArchive(): ?string
    {
        return $this->pathArchive;
    }

    public function setPathArchive(string $pathArchive): self
    {
        $this->pathArchive = $pathArchive;

        return $this;
    }

    public function getEstado(): ?string
    {
        return $this->estado;
    }

    public function setEstado(?string $estado): self
    {
        $this->estado = $estado;

        return $this;
    }

    public function getAnio(): ?string
    {
        return $this->anio;
    }

    public function setAnio(string $anio): self
    {
        $this->anio = $anio;

        return $this;
    }

    public function getMes(): ?string
    {
        return $this->mes;
    }

    public function setMes(string $mes): self
    {
        $this->mes = $mes;

        return $this;
    }
}
