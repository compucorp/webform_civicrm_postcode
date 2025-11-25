<?php

namespace Drupal\webform_civicrm_postcode\Plugin\WebformElement;

use Drupal\webform\Plugin\WebformElementBase;
use Drupal\webform\WebformSubmissionInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a 'Civipostcode' element.
 *
 * @WebformElement(
 *   id = "civipostcode",
 *   label = @Translation("Civi Postcode"),
 *   description = @Translation("A custom postcode lookup element."),
 *   category = @Translation("Custom"),
 * )
 */
class Civipostcode extends WebformElementBase {

  /**
   * The postcode utility service.
   *
   * @var \Drupal\webform_civicrm_postcode\Utils\PostcodeUtils
   */
  protected $utils;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    $instance = parent::create($container, $configuration, $plugin_id, $plugin_definition);
    $instance->utils = $container->get('webform_civicrm_postcode.utils');
    return $instance;
  }

  /**
   * {@inheritdoc}
   */
  public function prepare(array &$element, WebformSubmissionInterface $webform_submission = NULL) {
    // Always a textfield + input.
    $element['#type'] = 'textfield';
    $element['#input'] = TRUE;

    // Only enable autocomplete if the extension is enabled.
    if ($this->utils->isPostcodeLookupExtensionEnabled()) {
      $provider = $this->utils->getPostCodeLookupSettings()['provider'] ?? NULL;
      if ($provider) {
        $host = \Drupal::request()->getSchemeAndHttpHost();

        // Attach autocomplete processing handlers.
        $element['#process'] = [
          ['Drupal\Core\Render\Element\Textfield', 'processAutocomplete'],
          ['Drupal\Core\Render\Element\Textfield', 'processAjaxForm'],
          ['Drupal\Core\Render\Element\Textfield', 'processPattern'],
        ];

        // Autocomplete route → your controller.
        $element['#autocomplete_route_name'] = 'webform_civicrm_postcode.autocomplete';
        $element['#autocomplete_route_parameters'] = [];

        // Attach JS libraries.
        $element['#attached']['library'][] = 'core/drupal.autocomplete';
        $element['#attached']['library'][] = 'webform_civicrm_postcode/civipostcode_autocomplete';

        // Add class for JS behavior.
        $element['#attributes']['class'][] = 'civipostcode-autocomplete';

        // Provide JS settings (for the "get details" call).
        $element['#attached']['drupalSettings']['civipostcode'] = [
          'apiUrl' => "{$host}/civicrm/{$provider}/ajax/get?json=1",
        ];
      }
    }

    parent::prepare($element, $webform_submission);
  }

}
