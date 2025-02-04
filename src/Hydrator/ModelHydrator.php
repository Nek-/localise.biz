<?php

declare(strict_types=1);

/*
 * This software may be modified and distributed under the terms
 * of the MIT license. See the LICENSE file for details.
 */

namespace FAPI\Localise\Hydrator;

use FAPI\Localise\Exception\HydrationException;
use FAPI\Localise\Model\CreatableFromArray;
use Psr\Http\Message\ResponseInterface;

/**
 * Hydrate an HTTP response to domain object.
 *
 * @author Tobias Nyholm <tobias.nyholm@gmail.com>
 */
final class ModelHydrator implements Hydrator
{
    /**
     * @return mixed
     */
    public function hydrate(ResponseInterface $response, string $class)
    {
        $body = $response->getBody()->__toString();
        if (0 !== strpos($response->getHeaderLine('Content-Type'), 'application/json')) {
            throw new HydrationException('The ModelHydrator cannot hydrate response with Content-Type:'.$response->getHeaderLine('Content-Type'));
        }

        $data = json_decode($body, true);
        if (JSON_ERROR_NONE !== json_last_error()) {
            throw new HydrationException(sprintf('Error (%d) when trying to json_decode response', json_last_error()));
        }

        if (is_subclass_of($class, CreatableFromArray::class)) {
            $object = call_user_func($class.'::createFromArray', $data);
        } else {
            $object = new $class($data);
        }

        return $object;
    }
}
