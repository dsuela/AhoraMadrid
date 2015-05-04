<?php

namespace AhoraMadrid\InscripcionInterventoresBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use AhoraMadrid\InscripcionInterventoresBundle\Validator\Constraints as AMAssert;

/**
 * Inscrito
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="AhoraMadrid\InscripcionInterventoresBundle\Entity\InscritoRepository")
 * @ORM\HasLifecycleCallbacks
 */
class Inscrito
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
     * @var boolean
     *
     * @ORM\Column(name="aprobada", type="boolean")
     */
    private $aprobada = false;

    /**
     * @var string
     *
     * @ORM\Column(name="documentoIdentidad", type="string", length=10)
     * @AMAssert\DniNie
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
     * @ORM\Column(name="profesion", type="string", length=40)
     */
    private $profesion;

    /**
     * @var integer
     *
     * @ORM\Column(name="edad", type="integer", length=2)
     */
    private $edad;

    /**
     * @var string
     *
     * @ORM\Column(name="nacionalidad", type="string", length=40)
     */
    private $nacionalidad;

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
     * @ORM\ManyToOne(targetEntity="Distrito")
     * @ORM\JoinColumn(name="id_distrito", referencedColumnName="id")
     */
    private $distrito;

    /**
     * @var string
     *
     * @ORM\Column(name="experienciaPrevia", type="string", length=40, nullable=true)
     */
    private $experienciaPrevia;

    /**
     * @var string
     *
     * @ORM\Column(name="ruta", type="string", length=255, nullable=true)
     */
    private $ruta;
    
    public function getAbsolutePath(){
    	//return $this->getUploadRootDir().'/'.$this->ruta;
    	return null === $this->ruta
    	? null
    	: $this->getUploadRootDir().'/'. $this->ruta;
    }
    
    public function getWebPath(){
    	return null === $this->ruta
    	? null
    	: $this->getUploadDir().'/'. $this->ruta;
    }
    
    protected function getUploadRootDir(){
    	// la ruta absoluta del directorio donde se deben
    	// guardar los archivos cargados
    	return __DIR__.'/../../../../web/'.$this->getUploadDir();
    }
    
    protected function getUploadDir(){
    	// se deshace del __DIR__ para no meter la pata
    	// al mostrar el documento/imagen cargada en la vista.
    	return 'archivos/inscritos';
    }
    
    /**
     * @Assert\File(maxSize="4000000")
     */
    private $escaneado;
    
    /**
     * Set escaneado
     *
     * @param UploadedFile $escaneado
     *
     * @return Inscrito
     */
    public function setEscaneado(UploadedFile $escaneado = null){
    	$this->escaneado = $escaneado;
    	return $this;
    }
    
    public function establecerRuta(){
    	if (null !== $this->getEscaneado()) {
    		$this->ruta = $this->documentoIdentidad .".". $this->getEscaneado()->guessExtension();
    	}
    }
    
    public function upload(){
    	if (null === $this->getEscaneado()) {
    		return;
    	}
    	
    	//Se establece la ruta (nombre del archivo) si no está relleno
    	if($this->ruta === null){
    		$this->establecerRuta();
    	}
    
    	//Se copia el archivo a disco
    	$this->getEscaneado()->move($this->getUploadRootDir(), $this->ruta);
    	
    	//Se limpia la variable que tiene el archivo porque ya no la necesitamos
    	$this->escaneado = null;
    }
    
    /**
     * @ORM\PostRemove()
     */
    public function removeUpload(){
    	if (is_file($this->getUploadRootDir() ."/". $this->ruta)) {
    		unlink($this->getUploadRootDir() ."/". $this->ruta);
    	}
    }
    
    /**
     * Get escaneado
     *
     * @return \file
     */
    public function getEscaneado(){
    	return $this->escaneado;
    }
    

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
     * @return Inscrito
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
     * @return Inscrito
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
     * Set aprobada
     *
     * @param boolean $aprobada
     *
     * @return Inscrito
     */
    public function setAprobada($aprobada)
    {
        $this->aprobada = $aprobada;

        return $this;
    }

    /**
     * Get aprobada
     *
     * @return boolean
     */
    public function getAprobada()
    {
        return $this->aprobada;
    }

    /**
     * Set documentoIdentidad
     *
     * @param string $documentoIdentidad
     *
     * @return Inscrito
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
     * @return Inscrito
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
     * @return Inscrito
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
     * Set profesion
     *
     * @param string $profesion
     *
     * @return Inscrito
     */
    public function setProfesion($profesion)
    {
        $this->profesion = $profesion;

        return $this;
    }

    /**
     * Get profesion
     *
     * @return string
     */
    public function getProfesion()
    {
        return $this->profesion;
    }

    /**
     * Set edad
     *
     * @param integer $edad
     *
     * @return Inscrito
     */
    public function setEdad($edad)
    {
        $this->edad = $edad;

        return $this;
    }

    /**
     * Get edad
     *
     * @return integer
     */
    public function getEdad()
    {
        return $this->edad;
    }

    /**
     * Set nacionalidad
     *
     * @param string $nacionalidad
     *
     * @return Inscrito
     */
    public function setNacionalidad($nacionalidad)
    {
        $this->nacionalidad = $nacionalidad;

        return $this;
    }

    /**
     * Get nacionalidad
     *
     * @return string
     */
    public function getNacionalidad()
    {
        return $this->nacionalidad;
    }

    /**
     * Set direcccion
     *
     * @param string $direcccion
     *
     * @return Inscrito
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
     * @return Inscrito
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
     * Set distrito
     *
     * @param integer $distrito
     *
     * @return Inscrito
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
     * Set experienciaPrevia
     *
     * @param string $experienciaPrevia
     *
     * @return Inscrito
     */
    public function setExperienciaPrevia($experienciaPrevia)
    {
        $this->experienciaPrevia = $experienciaPrevia;

        return $this;
    }

    /**
     * Get experienciaPrevia
     *
     * @return string
     */
    public function getExperienciaPrevia()
    {
        return $this->experienciaPrevia;
    }
}

