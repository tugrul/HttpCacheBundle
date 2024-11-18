<?php

namespace Tug\HttpCacheBundle\Registry;

use Symfony\Component\HttpFoundation\Request;
use Tug\HttpCacheBundle\Model\{Route, RouteMatch};

class Routes implements RoutesInterface
{
    protected array $routes = [];

    protected array $ignoredParamNames;

    protected array $allowedParamNames;

    public function setDefaultIgnoredParamNames(array $paramNames): void
    {
        $this->ignoredParamNames = $paramNames;
    }

    public function setDefaultAllowedParamNames(array $paramNames): void
    {
        $this->allowedParamNames = $paramNames;
    }

    public function setRoutes(array $routes): self
    {
        foreach ($routes as $routeName => $route) {

            if (!empty($route['name'])) {
                $routeName = $route['name'];
            }

            if (!is_string($routeName)) {
                throw new \RuntimeException('Not a proper route name');
            }

            $model = new Route();
            $model->setName($routeName);

            if (isset($route['allowed_query_names'])) {
                $model->setAllowedQueryNames($route['allowed_query_names']);
            }

            if (isset($route['allowed_param_names'])) {
                $model->setAllowedParamNames($route['allowed_param_names']);
            }

            if (isset($route['ignored_param_names'])) {
                $model->setIgnoredParamNames($route['ignored_param_names']);
            }

            $this->routes[] = $model;
        }

        return $this;
    }

    /**
     * @param Request $request
     * @return Route|null
     *
     * We use request instead of route name because configuration can have multiple route names using different parameters.
     */
    public function getRouteMatch(Request $request): ?RouteMatch
    {
        if (!$request->attributes->has('_route')) {
            return null;
        }

        $routeName = $request->attributes->get('_route');

        /**
         * @var $item Route
         */
        foreach ($this->routes as $item) {
            if ($item->getName() !== $routeName) {
                continue;
            }

            $queryParams = $item->getAllowedQueryNames() ?? [];

            foreach ($request->query as $key => $value) {
                // skip route configuration if the query name is not in allowed list
                if (!array_key_exists($key, $queryParams)) {
                    continue 2;
                }

                // overwrite default query param value
                $queryParams[$key] = $value;
            }

            $ignoredParams = array_merge($this->ignoredParamNames, $item->getIgnoredParamNames() ?? []);
            $allowedParams = array_merge($this->allowedParamNames, $item->getAllowedParamNames() ?? []);

            $routeParams = $request->attributes->get('_route_params', []);

            foreach ($routeParams as $key => $value) {
                if (in_array($key, $ignoredParams, true)) {
                    continue;
                }

                if (!array_key_exists($key, $allowedParams)) {
                    continue 2;
                }

                $allowedParams[$key] = $value;
            }

            return new RouteMatch($item, $queryParams, $allowedParams);
        }

        return null;
    }
}