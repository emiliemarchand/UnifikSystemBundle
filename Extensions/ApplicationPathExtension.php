<?php

namespace Unifik\SystemBundle\Extensions;

use Symfony\Component\HttpFoundation\Request;
use Unifik\SystemBundle\Entity\MappingRepository;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

use \Twig_Environment;
use \Twig_Function_Method;

/**
 * Used in the frontend to get application related information
 */
class ApplicationPathExtension extends \Twig_Extension {

    /**
     * @var doctrine
     */
    protected $doctrine;

    /**
     * @var Core
     */
    protected $systemCore;

    /**
     * @var MappingRepository
     */
    protected $mappingRepository;

    /**
     * @var Twig_Environment
     */
    protected $environment;

    /**
     * @var UrlGeneratorInterface
     */
    private $generator;

    /**
     * @var RouterAutoParametersHandler
     */
    private $autoParametersHandler;

    /**
     * Init Runtime
     *
     * @param Twig_Environment $environment
     */
    public function initRuntime(Twig_Environment $environment)
    {
        $this->environment = $environment;
    }

    public function getAppRoute($route_name, $app_id = null) {
        if (!$this->mappingRepository)
            $this->mappingRepository = $this->doctrine->getManager()->getRepository('UnifikSystemBundle:Mapping');
        $mapping = $this->mappingRepository->findOneBy(array(
            'app'=>($app_id ? $app_id: $this->systemCore->getApplicationCore()->getApp()->getId()),
            'type'=>'route',
            'target'=>$route_name,
        ), array('section'=>'ASC'));

        if ($mapping)
            return 'section_id_'.$mapping->getSection()->getId();

        // Fallback sur la premiere route disponible (?)
        $mapping = $this->mappingRepository->findOneBy(array(
            'type'=>'route',
            'target'=>$route_name,
        ), array('section'=>'ASC'));

        if ($mapping)
            return 'section_id_'.$mapping->getSection()->getId();

        // Rien de concluant
        return null;
    }

    public function getAppPath($route_name, $parameters = array(), $app_id = null, $relative = false) {
        $route = $this->getAppRoute($route_name, $app_id);
        $parameters = $this->autoParametersHandler->inject($parameters);
        return $this->generator->generate($route, $parameters, $relative ? UrlGeneratorInterface::RELATIVE_PATH : UrlGeneratorInterface::ABSOLUTE_PATH);
    }

    public function getAppUrl($route_name, $parameters = array(), $app_id = null, $relative = false) {
        $route = $this->getAppRoute($route_name, $app_id);
        $parameters = $this->autoParametersHandler->inject($parameters);
        return $this->generator->generate($route, $parameters, $relative ? UrlGeneratorInterface::NETWORK_PATH : UrlGeneratorInterface::ABSOLUTE_URL);
    }

    /**
     * Get Functions
     *
     * @return array
     */
    public function getFunctions()
    {
        return array(
            'app_route' => new Twig_Function_Method($this, 'getAppRoute'),
            'app_path' => new Twig_Function_Method($this, 'getAppPath'),
            'app_url' => new Twig_Function_Method($this, 'getAppUrl'),
        );
    }

    /**
     * Get Name
     *
     * @return string
     */
    public function getName()
    {
        return 'application_path_extension';
    }

    /**
     * Set the Doctrine Registry
     *
     * @param Registry $doctrine The Doctrine Registry
     */
    public function setDoctrine($doctrine)
    {
        $this->doctrine = $doctrine;
    }

    /**
     * @param mixed $systemCore
     */
    public function setSystemCore($systemCore)
    {
        $this->systemCore = $systemCore;
    }

    /**
     * @param mixed $generator
     */
    public function setGenerator($generator)
    {
        $this->generator = $generator;
    }

    /**
     * @param RouterAutoParametersHandler $autoParametersHandler
     */
    public function setAutoParametersHandler($autoParametersHandler)
    {
        $this->autoParametersHandler = $autoParametersHandler;
    }
}
