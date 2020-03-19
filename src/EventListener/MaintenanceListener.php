<?php

declare(strict_types=1);

/*
 * This file is part of the ekino/sonata project.
 *
 * (c) Ekino
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\HelpersBundle\EventListener;

use Lexik\Bundle\MaintenanceBundle\Drivers\DriverFactory;
use Lexik\Bundle\MaintenanceBundle\Exception\ServiceUnavailableException;
use Lexik\Bundle\MaintenanceBundle\Listener\MaintenanceListener as LexikMaintenanceListener;
use Sonata\PageBundle\Model\SiteInterface;
use Sonata\PageBundle\Site\SiteSelectorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;

/**
 * Class MaintenanceListener.
 *
 * @author Quentin Belot <quentin.belot@ekino.com>
 */
class MaintenanceListener extends LexikMaintenanceListener
{
    /**
     * @var SiteSelectorInterface
     */
    private $siteSelector;

    /**
     * MaintenanceListener constructor.
     */
    public function __construct(
        SiteSelectorInterface $siteSelector,
        DriverFactory $driverFactory,
        ?string $path = null,
        ?string $host = null,
        ?array $ips = null,
        array $query = [],
        array $cookie = [],
        ?string $route = null,
        array $attributes = [],
        ?int $http_code = null,
        ?string $http_status = null,
        ?string $http_exception_message = null,
        bool $debug = false
    ) {
        parent::__construct($driverFactory, $path, $host, $ips, $query, $cookie, $route, $attributes, $http_code, $http_status, $http_exception_message, $debug);

        $this->siteSelector = $siteSelector;
    }

    /**
     * {@inheritdoc}
     */
    public function onKernelRequest(GetResponseEvent $event)
    {
        if (!$event->isMasterRequest()) {
            return;
        }

        $request = $event->getRequest();

        if ($this->checkAuthorizedOptions($request)) {
            return;
        }

        // Get driver class defined in your configuration
        $driver = $this->driverFactory->getDriver();
        $site   = $this->siteSelector->retrieve();

        if (!$site instanceof SiteInterface) {
            return;
        }

        $driver->setSiteId($site->getId());

        if ($driver->decide()) {
            $this->handleResponse = true;
            throw new ServiceUnavailableException($this->http_exception_message);
        }
    }

    private function checkAuthorizedOptions(Request $request): bool
    {
        $response = $this->checkPattern($this->query, $request) ||
            $this->checkPattern($this->cookie, $request->cookies) ||
            $this->checkPattern($this->attributes, $request->attributes)
        ;

        if (null !== $this->path && !empty($this->path) && preg_match('{'.$this->path.'}', rawurldecode($request->getPathInfo()))) {
            $response = true;
        }

        if (null !== $this->host && !empty($this->host) && preg_match('{'.$this->host.'}i', $request->getHost())) {
            $response = true;
        }

        if (0 !== \count((array) $this->ips) && $this->checkIps($request->getClientIp(), $this->ips)) {
            $response = true;
        }

        $route = $request->get('_route');
        if (null !== $this->route && preg_match('{'.$this->route.'}', $route) || (true === $this->debug && '_' === $route[0])) {
            $response = true;
        }

        return $response;
    }

    /**
     * @param mixed $data
     * @param mixed $source
     */
    private function checkPattern($data, $source): bool
    {
        if (\is_array($data)) {
            foreach ($data as $key => $pattern) {
                if (!empty($pattern) && preg_match('{'.$pattern.'}', $source->get($key))) {
                    return true;
                }
            }
        }

        return false;
    }
}
