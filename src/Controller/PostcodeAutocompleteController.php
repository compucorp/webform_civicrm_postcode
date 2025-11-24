<?php

namespace Drupal\webform_civicrm_postcode\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use GuzzleHttp\ClientInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class PostcodeAutocompleteController extends ControllerBase {

  /**
   * @var \GuzzleHttp\ClientInterface
   */
  protected $httpClient;

  /**
   * @var \Drupal\webform_civicrm_postcode\Utils
   */
  protected $utils;

  /**
   * Construct a Postcode Autocomplete Controller.
   */
  public function __construct(ClientInterface $http_client, $utils) {
    $this->httpClient = $http_client;
    $this->utils = $utils;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('http_client'),
      $container->get('webform_civicrm_postcode.utils')
    );
  }

  /**
   * Handle autocomplete request.
   */
  public function handleAutocomplete(Request $request) {
    $search = trim($request->query->get('q', ''));

    if (strlen($search) < 2) {
      return new JsonResponse([]);
    }

    $lookupProvider = $this->utils->getPostCodeLookupSettings()['provider'];
    $lookupUrl = sprintf(
      '%s/civicrm/%s/ajax/search?json=1&term=%s',
      $request->getSchemeAndHttpHost(), $lookupProvider, urlencode($search)
    );

    try {
      $response = $this->httpClient->get($lookupUrl, ['timeout' => 10]);

      if ($response->getStatusCode() !== 200) {
        return new JsonResponse([]);
      }

      $data = json_decode($response->getBody()->getContents(), TRUE);
      if (!is_array($data)) {
        return new JsonResponse([]);
      }

      return new JsonResponse($data);
    }
    catch (\Exception $e) {
      return new JsonResponse([]);
    }
  }

}
