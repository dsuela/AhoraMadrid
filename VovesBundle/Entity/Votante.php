<?php

namespace AhoraMadrid\VovesBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use AhoraMadrid\InscripcionInterventoresBundle\Entity\Distrito as Distrito;

/**
 * Votante
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="AhoraMadrid\VovesBundle\Entity\VotanteRepository")
 */
class Votante
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="nombre", type="string", length=40)
     */
    private $nombre;

    /**
     * @var string
     *
     * @ORM\Column(name="apellidos", type="string", length=80)
     */
    private $apellidos;

    /**
     * @var string
     *
     * @ORM\Column(name="documentoIdentidad", type="string", length=10, nullable=true)
     */
    private $documentoIdentidad;

    /**
     * @var string
     *
     * @ORM\Column(name="correoElectronico", type="string", length=80)
     */
    private $correoElectronico;

    /**
     * @var string
     *
     * @ORM\Column(name="telefono", type="string", length=20)
     */
    private $telefono;

    /**
     * @var string
     *
     * @ORM\Column(name="direcccion", type="string", length=255)
     */
    private $direcccion;

    /**
     * @var integer
     *
     * @ORM\Column(name="codigoPostal", type="integer", length=10)
     */
    private $codigoPostal;

    /**
     * @var string
     *
     * @ORM\Column(name="poblacion", type="string", length=40)
     */
    private $poblacion;

    /**
     * @ORM\ManyToOne(targetEntity="AhoraMadrid\InscripcionInterventoresBundle\Entity\Distrito")
     * @ORM\JoinColumn(name="id_distrito", referencedColumnName="id")
     */
    private $distrito;

    /**
     * @var boolean
     *
     * @ORM\Column(name="recibirInformacion", type="boolean")
     */
    private $recibirInformacion;

    /**
     * @var boolean
     *
     * @ORM\Column(name="validada", type="boolean")
     */
    private $validada = false;

    /**
     * @var boolean
     *
     * @ORM\Column(name="votado", type="boolean")
     */
    private $votado = false;


    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set nombre
     *
     * @param string $nombre
     *
     * @return Votante
     */
    public function setNombre($nombre)
    {
        $this->nombre = $nombre;

        return $this;
    }

    /**
     * Get nombre
     *
     * @return string
     */
    public function getNombre()
    {
        return $this->nombre;
    }

    /**
     * Set apellidos
     *
     * @param string $apellidos
     *
     * @return Votante
     */
    public function setApellidos($apellidos)
    {
        $this->apellidos = $apellidos;

        return $this;
    }

    /**
     * Get apellidos
     *
     * @return string
     */
    public function getApellidos()
    {
        return $this->apellidos;
    }

    /**
     * Set documentoIdentidad
     *
     * @param string $documentoIdentidad
     *
     * @return Votante
     */
    public function setDocumentoIdentidad($documentoIdentidad)
    {
        $this->documentoIdentidad = $documentoIdentidad;

        return $this;
    }

    /**
     * Get documentoIdentidad
     *
     * @return string
     */
    public function getDocumentoIdentidad()
    {
        return $this->documentoIdentidad;
    }

    /**
     * Set correoElectronico
     *
     * @param string $correoElectronico
     *
     * @return Votante
     */
    public function setCorreoElectronico($correoElectronico)
    {
        $this->correoElectronico = $correoElectronico;

        return $this;
    }

    /**
     * Get correoElectronico
     *
     * @return string
     */
    public function getCorreoElectronico()
    {
        return $this->correoElectronico;
    }

    /**
     * Set telefono
     *
     * @param string $telefono
     *
     * @return Votante
     */
    public function setTelefono($telefono)
    {
        $this->telefono = $telefono;

        return $this;
    }

    /**
     * Get telefono
     *
     * @return string
     */
    public function getTelefono()
    {
        return $this->telefono;
    }

    /**
     * Set direcccion
     *
     * @param string $direcccion
     *
     * @return Votante
     */
    public function setDirecccion($direcccion)
    {
        $this->direcccion = $direcccion;

        return $this;
    }

    /**
     * Get direcccion
     *
     * @return string
     */
    public function getDirecccion()
    {
        return $this->direcccion;
    }

    /**
     * Set codigoPostal
     *
     * @param integer $codigoPostal
     *
     * @return Votante
     */
    public function setCodigoPostal($codigoPostal)
    {
        $this->codigoPostal = $codigoPostal;

        return $this;
    }

    /**
     * Get codigoPostal
     *
     * @return integer
     */
    public function getCodigoPostal()
    {
        return $this->codigoPostal;
    }

    /**
     * Set poblacion
     *
     * @param string $poblacion
     *
     * @return Votante
     */
    public function setPoblacion($poblacion)
    {
        $this->poblacion = $poblacion;

        return $this;
    }

    /**
     * Get poblacion
     *
     * @return string
     */
    public function getPoblacion()
    {
        return $this->poblacion;
    }

    /**
     * Set distrito
     *
     * @param integer $distrito
     *
     * @return Votante
     */
    public function setDistrito($distrito)
    {
        $this->distrito = $distrito;

        return $this;
    }

    /**
     * Get distrito
     *
     * @return integer
     */
    public function getDistrito()
    {
        return $this->distrito;
    }

    /**
     * Set recibirInformacion
     *
     * @param boolean $recibirInformacion
     *
     * @return Votante
     */
    public function setRecibirInformacion($recibirInformacion)
    {
        $this->recibirInformacion = $recibirInformacion;

        return $this;
    }

    /**
     * Get recibirInformacion
     *
     * @return boolean
     */
    public function getRecibirInformacion()
    {
        return $this->recibirInformacion;
    }

    /**
     * Set validada
     *
     * @param boolean $validada
     *
     * @return Votante
     */
    public function setValidada($validada)
    {
        $this->validada = $validada;

        return $this;
    }

    /**
     * Get validada
     *
     * @return boolean
     */
    public function getValidada()
    {
        return $this->validada;
    }

    /**
     * Set votado
     *
     * @param boolean $votado
     *
     * @return Votante
     */
    public function setVotado($votado)
    {
        $this->votado = $votado;

        return $this;
    }

    /**
     * Get votado
     *
     * @return boolean
     */
    public function getVotado()
    {
        return $this->votado;
    }
}

