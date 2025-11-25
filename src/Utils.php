<?php

namespace Drupal\webform_civicrm_postcode;

class Utils {

  public function __construct() {
    \Drupal::service('civicrm')->initialize();
  }

  /**
   * Check if the postcode extension is enabled.
   *
   * @return bool
   */
  public function isPostcodeLookupExtensionEnabled(): bool {
    return \CRM_Extension_System::singleton()->getMapper()->isActiveModule(
      'civicrmpostcodelookup'
    );
  }

  /**
   * Get postal code lookup settings.
   *
   * @return array
   */
  public function getPostCodeLookupSettings() {
    return unserialize(\Civi::settings()->get('api_details'));
  }

}
