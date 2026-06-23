(function (window, $) {
    'use strict';

    var UTILS_URL = 'https://cdn.jsdelivr.net/npm/intl-tel-input@23.0.10/build/js/utils.js';
    var SELECTORS = 'input[name="phone"], input[name="enquiry_phone"], input[name="whatsapp_full"], input.bravo-intl-phone';

    function getInstance(input) {
        if (!input || !window.intlTelInputGlobals) {
            return null;
        }
        return window.intlTelInputGlobals.getInstance(input);
    }

    function getNumber(input) {
        if (!input) {
            return '';
        }
        var iti = getInstance(input);
        if (iti) {
            return iti.getNumber() || input.value || '';
        }
        return input.value || '';
    }

    function initInput(input) {
        if (!input || input.dataset.itiInit || typeof window.intlTelInput === 'undefined') {
            return;
        }
        if (input.type === 'hidden' || input.disabled) {
            return;
        }

        var iti = window.intlTelInput(input, {
            separateDialCode: true,
            nationalMode: false,
            initialCountry: 'in',
            preferredCountries: ['in', 'ae', 'lk', 'gb', 'us', 'sg', 'my'],
            utilsScript: UTILS_URL
        });

        if (input.value && input.value.trim()) {
            try {
                iti.setNumber(input.value.trim());
            } catch (e) {
                /* keep raw value */
            }
        }

        input.dataset.itiInit = '1';
        input.classList.add('bravo-intl-phone');
    }

    function initAll(root) {
        var scope = root && root.querySelectorAll ? root : document;
        scope.querySelectorAll(SELECTORS).forEach(function (input) {
            initInput(input);
        });
    }

    function syncWhatsappFields(formEl) {
        if (!formEl) {
            return;
        }
        var input = formEl.querySelector('input.bravo-intl-whatsapp, input[name="whatsapp_full"]');
        if (!input) {
            return;
        }
        var codeInput = formEl.querySelector('input[name="whatsapp_code"]');
        var numInput = formEl.querySelector('input[name="whatsapp"]');
        if (!codeInput || !numInput) {
            return;
        }
        var iti = getInstance(input);
        if (!iti) {
            return;
        }
        var full = getNumber(input);
        if (!full) {
            codeInput.value = '';
            numInput.value = '';
            return;
        }
        var dialCode = (iti.getSelectedCountryData() || {}).dialCode || '';
        var digits = full.replace(/\D/g, '');
        var national = dialCode && digits.indexOf(dialCode) === 0
            ? digits.substring(dialCode.length)
            : digits;
        codeInput.value = dialCode;
        numInput.value = national;
    }

    function syncInForm(form) {
        if (!form) {
            return;
        }
        var el = form.jquery ? form[0] : form;
        if (!el || !el.querySelectorAll) {
            return;
        }
        el.querySelectorAll(SELECTORS).forEach(function (input) {
            if (input.classList.contains('bravo-intl-whatsapp') || input.name === 'whatsapp_full') {
                return;
            }
            var number = getNumber(input);
            if (number) {
                input.value = number;
            }
        });
        syncWhatsappFields(el);
    }

    window.BravoPhoneIntl = {
        init: initAll,
        initInput: initInput,
        getNumber: getNumber,
        getValue: getNumber,
        getValueByName: function (name, root) {
            var scope = root && root.querySelector ? root : document;
            var input = scope.querySelector('[name="' + name + '"]');
            return getNumber(input);
        },
        syncForm: syncInForm,
        syncWhatsapp: syncWhatsappFields
    };

    $(function () {
        initAll(document);
    });

    $(document).on('shown.bs.modal', '.modal', function () {
        setTimeout(function () {
            initAll(this);
        }.bind(this), 150);
    });

    $(document).on('submit', 'form', function () {
        syncInForm(this);
    });

    $(document).on('click', '.bravo-form-register [type=submit], .bravo-form-register-vendor [type=submit], .btn-submit-enquiry', function () {
        var $container = $(this).closest('form');
        if (!$container.length) {
            $container = $(this).closest('.enquiry_form_modal_form, .modal-content, .modal');
        }
        if ($container.length) {
            syncInForm($container[0]);
        }
    });

})(window, jQuery);
