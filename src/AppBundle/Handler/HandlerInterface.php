<?php

namespace AppBundle\Handler;

/**
 * Interface HandlerInterface
 * @package AppBundle\Handler
 */
interface HandlerInterface
{
    /**
     * @param int             $id
     * @return mixed
     */
    public function get($id);

    /**
     * @param int             $limit
     * @param int             $offset
     * @return mixed
     */
    public function all($limit, $offset);

    /**
     * @param array           $parameters
     * @param array           $options
     * @return mixed
     */
    public function post(array $parameters, array $options);

    /**
     * @param mixed           $resource
     * @param array           $parameters
     * @param array           $options
     * @return mixed
     */
    public function put($resource, array $parameters, array $options);

    /**
     * @param mixed           $resource
     * @param array           $parameters
     * @param array           $options
     * @return mixed
     */
    public function patch($resource, array $parameters, array $options);

    /**
     * @param mixed           $resource
     * @return mixed
     */
    public function delete($resource);
}