(function (Drupal, $, once) {
  Drupal.behaviors.civipostcodeAutocomplete = {
    attach: function (context, settings) {
      const $postcodeFields = $(once('civipostcodeAutocomplete', '.civipostcode-autocomplete', context));

      $postcodeFields.each((index, element) => {
        const $input = $(element);
        const elementId = $input.attr('id');
        const { civicrmSeq, contactSeq } = extractSequences(elementId);

        if (!civicrmSeq || !contactSeq) return;

        $input.on('autocompleteselect', (event, ui) => {
          if (!ui.item.id) return;
          $.ajax({
            dataType: 'json',
            data: {id: ui.item.id},
            url: drupalSettings.civipostcode.apiUrl,
            success: function (data) {
              populateAddress(data.address, civicrmSeq, contactSeq);
            }
          });

          return false;
        });
      });

      /**
       * Extract CiviCRM and Contact sequences from the element ID.
       * Returns an object: { civicrmSeq, contactSeq }
       */
      function extractSequences(id) {
        const parts = id.split('-');
        let civicrmSeq = null;
        let contactSeq = null;

        parts.forEach((part, index) => {
          if (parts[index - 1] === 'civicrm') civicrmSeq = part;
          if (parts[index - 1] === 'contact') contactSeq = part;
        });

        return { civicrmSeq, contactSeq };
      }

      /**
       * Populate address fields based on CiviCRM and Contact sequence.
       */
      function populateAddress(address, civicrmSeq, contactSeq) {
        const fieldMap = {
          street_address: `civicrm-${civicrmSeq}-contact-${contactSeq}-address-street-address`,
          supplemental_address_1: `civicrm-${civicrmSeq}-contact-${contactSeq}-address-supplemental-address-1`,
          supplemental_address_2: `civicrm-${civicrmSeq}-contact-${contactSeq}-address-supplemental-address-2`,
          supplemental_address_3: `civicrm-${civicrmSeq}-contact-${contactSeq}-address-supplemental-address-3`,
          city: `civicrm-${civicrmSeq}-contact-${contactSeq}-address-city`,
          postcode: `civicrm-${civicrmSeq}-contact-${contactSeq}-address-postal-code`,
          county: `civicrm-${civicrmSeq}-contact-${contactSeq}-address-state-province-id`,
          country_id: `civicrm-${civicrmSeq}-contact-${contactSeq}-address-country-id`,
        };

        Object.entries(fieldMap).forEach(([key, selector]) => {
          const $field = $(`[id*="${selector}"]`);
          // Always set a value — either the returned value or an empty string.
          $field.val(address[key] ?? '');
        });

        // Handle special cases if CiviCRM uses different keys
        if (address.town) {
          $(`[id*="civicrm-${civicrmSeq}-contact-${contactSeq}-address-city"]`).val(address.town);
        }
        if (address.state_province_abbreviation) {
          $(`[id*="civicrm-${civicrmSeq}-contact-${contactSeq}-address-state-province-id"]`).val(address.state_province_abbreviation);
        }
      }
    }
  };
})(Drupal, jQuery, once);
