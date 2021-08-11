<?php
namespace App\Entity;

/**
 * @ORM\Entity(repositoryClass=UserBusquedaRepository::class)
 */
class UserBusqueda
{
    private $buscar;
    
    
    function getBuscar() {
        return $this->buscar;
    }

    function setBuscar($buscar): void {
        $this->buscar = $buscar;
    }
}
