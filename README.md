# Postcode lookup for webforms

## Overview
Integrates [civipostcodelookup](https://github.com/veda-consulting/uk.co.vedaconsulting.module.civicrmpostcodelookup) with [webform_civicrm](https://www.drupal.org/project/webform_civicrm).

## Steps
This extension provides a widget type called "Civi Postcode". To use this you will need to follow the steps below:

1. Add address fields for contacts using webform_civicrm.
2. Go to "webform" tab and change widget type of postal code field from textfield to Civi Postcode and save it.
3. Go to webform and enter a valid UK postcode. For example: E1 6LA
4. Choose any address from lookup list and it will automatically fill chosen address in respective fields.

## CiviCRM Dependencies
As this module uses resources provided by [civipostcodelookup](https://github.com/veda-consulting/uk.co.vedaconsulting.module.civicrmpostcodelookup), this needs to be enabled under CiviCRM.

## Drupal Dependencies
This module depends on webform_civicrm. Please download and enable it from here: https://www.drupal.org/project/webform_civicrm
