<?php

/*
 *
 *
 * This software may be modified and distributed under the terms
 * of the MIT license.  See the LICENSE file for details.
 */

namespace FAPI\Localise\Api;

use FAPI\Localise\Exception;
use FAPI\Localise\Model\Translation\Translation as TranslationModel;
use FAPI\Localise\Model\Translation\TranslationDeleted;
use Psr\Http\Message\ResponseInterface;

/**
 * @author Tobias Nyholm <tobias.nyholm@gmail.com>
 */
class Translation extends HttpApi
{
    /**
     * Get a translation.
     * {@link https://localise.biz/api/docs/translations/gettranslation}.
     *
     * @return TranslationModel|ResponseInterface
     *
     * @throws Exception
     */
    public function get(string $projectKey, string $id, string $locale)
    {
        $response = $this->httpGet(sprintf('/api/translations/%s/%s?key=%s', rawurlencode($id), $locale, $projectKey));

        if (!$this->hydrator) {
            return $response;
        }

        if (200 !== $response->getStatusCode()) {
            $this->handleErrors($response);
        }

        return $this->hydrator->hydrate($response, TranslationModel::class);
    }

    /**
     * Create a new translation.
     * {@link https://localise.biz/api/docs/translations/translate}.
     *
     * @return TranslationModel|ResponseInterface
     *
     * @throws Exception
     */
    public function create(string $projectKey, string $id, string $locale, string $translation)
    {
        $response = $this->httpPostRaw(sprintf('/api/translations/%s/%s?key=%s', rawurlencode($id), $locale, $projectKey), $translation);
        if (!$this->hydrator) {
            return $response;
        }

        if ($response->getStatusCode() >= 400) {
            $this->handleErrors($response);
        }

        return $this->hydrator->hydrate($response, TranslationModel::class);
    }

    /**
     * Delete translation
     * {@link https://localise.biz/api/docs/translations/untranslate}.
     *
     * @return TranslationDeleted|ResponseInterface
     *
     * @throws Exception
     */
    public function delete(string $projectKey, string $id, string $locale)
    {
        $response = $this->httpDelete(sprintf('/api/translations/%s/%s?key=%s', rawurlencode($id), $locale, $projectKey));
        if (!$this->hydrator) {
            return $response;
        }

        if (200 !== $response->getStatusCode()) {
            $this->handleErrors($response);
        }

        return $this->hydrator->hydrate($response, TranslationDeleted::class);
    }
}
