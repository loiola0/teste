/*! elementor-pro - v3.3.0 - 06-06-2021 */
(self["webpackChunkelementor_pro"] = self["webpackChunkelementor_pro"] || []).push([["form"],{

/***/ "../modules/forms/assets/js/frontend/handlers/fields/data-time-field-base.js":
/*!***********************************************************************************!*\
  !*** ../modules/forms/assets/js/frontend/handlers/fields/data-time-field-base.js ***!
  \***********************************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime-corejs2/helpers/interopRequireDefault */ "../node_modules/@babel/runtime-corejs2/helpers/interopRequireDefault.js");

var _Object$defineProperty = __webpack_require__(/*! @babel/runtime-corejs2/core-js/object/define-property */ "../node_modules/@babel/runtime-corejs2/core-js/object/define-property.js");

_Object$defineProperty(exports, "__esModule", {
  value: true
});

exports.default = void 0;

__webpack_require__(/*! core-js/modules/es6.array.find */ "../node_modules/core-js/modules/es6.array.find.js");

var _classCallCheck2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime-corejs2/helpers/classCallCheck */ "../node_modules/@babel/runtime-corejs2/helpers/classCallCheck.js"));

var _createClass2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime-corejs2/helpers/createClass */ "../node_modules/@babel/runtime-corejs2/helpers/createClass.js"));

var _get3 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime-corejs2/helpers/get */ "../node_modules/@babel/runtime-corejs2/helpers/get.js"));

var _getPrototypeOf2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime-corejs2/helpers/getPrototypeOf */ "../node_modules/@babel/runtime-corejs2/helpers/getPrototypeOf.js"));

var _inherits2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime-corejs2/helpers/inherits */ "../node_modules/@babel/runtime-corejs2/helpers/inherits.js"));

var _createSuper2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime-corejs2/helpers/createSuper */ "../node_modules/@babel/runtime-corejs2/helpers/createSuper.js"));

var DataTimeFieldBase = /*#__PURE__*/function (_elementorModules$fro) {
  (0, _inherits2.default)(DataTimeFieldBase, _elementorModules$fro);

  var _super = (0, _createSuper2.default)(DataTimeFieldBase);

  function DataTimeFieldBase() {
    (0, _classCallCheck2.default)(this, DataTimeFieldBase);
    return _super.apply(this, arguments);
  }

  (0, _createClass2.default)(DataTimeFieldBase, [{
    key: "getDefaultSettings",
    value: function getDefaultSettings() {
      return {
        selectors: {
          fields: this.getFieldsSelector()
        },
        classes: {
          useNative: 'elementor-use-native'
        }
      };
    }
  }, {
    key: "getDefaultElements",
    value: function getDefaultElements() {
      var _this$getDefaultSetti = this.getDefaultSettings(),
          selectors = _this$getDefaultSetti.selectors;

      return {
        $fields: this.$element.find(selectors.fields)
      };
    }
  }, {
    key: "addPicker",
    value: function addPicker(element) {
      var _this$getDefaultSetti2 = this.getDefaultSettings(),
          classes = _this$getDefaultSetti2.classes,
          $element = jQuery(element);

      if ($element.hasClass(classes.useNative)) {
        return;
      }

      element.flatpickr(this.getPickerOptions(element));
    }
  }, {
    key: "onInit",
    value: function onInit() {
      var _get2,
          _this = this;

      for (var _len = arguments.length, args = new Array(_len), _key = 0; _key < _len; _key++) {
        args[_key] = arguments[_key];
      }

      (_get2 = (0, _get3.default)((0, _getPrototypeOf2.default)(DataTimeFieldBase.prototype), "onInit", this)).call.apply(_get2, [this].concat(args));

      this.elements.$fields.each(function (index, element) {
        return _this.addPicker(element);
      });
    }
  }]);
  return DataTimeFieldBase;
}(elementorModules.frontend.handlers.Base);

exports.default = DataTimeFieldBase;

/***/ }),

/***/ "../modules/forms/assets/js/frontend/handlers/fields/date.js":
/*!*******************************************************************!*\
  !*** ../modules/forms/assets/js/frontend/handlers/fields/date.js ***!
  \*******************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime-corejs2/helpers/interopRequireDefault */ "../node_modules/@babel/runtime-corejs2/helpers/interopRequireDefault.js");

var _Object$defineProperty = __webpack_require__(/*! @babel/runtime-corejs2/core-js/object/define-property */ "../node_modules/@babel/runtime-corejs2/core-js/object/define-property.js");

_Object$defineProperty(exports, "__esModule", {
  value: true
});

exports.default = void 0;

var _classCallCheck2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime-corejs2/helpers/classCallCheck */ "../node_modules/@babel/runtime-corejs2/helpers/classCallCheck.js"));

var _createClass2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime-corejs2/helpers/createClass */ "../node_modules/@babel/runtime-corejs2/helpers/createClass.js"));

var _inherits2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime-corejs2/helpers/inherits */ "../node_modules/@babel/runtime-corejs2/helpers/inherits.js"));

var _createSuper2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime-corejs2/helpers/createSuper */ "../node_modules/@babel/runtime-corejs2/helpers/createSuper.js"));

var _dataTimeFieldBase = _interopRequireDefault(__webpack_require__(/*! ./data-time-field-base */ "../modules/forms/assets/js/frontend/handlers/fields/data-time-field-base.js"));

var DateField = /*#__PURE__*/function (_FieldBase) {
  (0, _inherits2.default)(DateField, _FieldBase);

  var _super = (0, _createSuper2.default)(DateField);

  function DateField() {
    (0, _classCallCheck2.default)(this, DateField);
    return _super.apply(this, arguments);
  }

  (0, _createClass2.default)(DateField, [{
    key: "getFieldsSelector",
    value: function getFieldsSelector() {
      return '.elementor-date-field';
    }
  }, {
    key: "getPickerOptions",
    value: function getPickerOptions(element) {
      var $element = jQuery(element);
      return {
        minDate: $element.attr('min') || null,
        maxDate: $element.attr('max') || null,
        allowInput: true
      };
    }
  }]);
  return DateField;
}(_dataTimeFieldBase.default);

exports.default = DateField;

/***/ }),

/***/ "../modules/forms/assets/js/frontend/handlers/fields/time.js":
/*!*******************************************************************!*\
  !*** ../modules/forms/assets/js/frontend/handlers/fields/time.js ***!
  \*******************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime-corejs2/helpers/interopRequireDefault */ "../node_modules/@babel/runtime-corejs2/helpers/interopRequireDefault.js");

var _Object$defineProperty = __webpack_require__(/*! @babel/runtime-corejs2/core-js/object/define-property */ "../node_modules/@babel/runtime-corejs2/core-js/object/define-property.js");

_Object$defineProperty(exports, "__esModule", {
  value: true
});

exports.default = void 0;

var _classCallCheck2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime-corejs2/helpers/classCallCheck */ "../node_modules/@babel/runtime-corejs2/helpers/classCallCheck.js"));

var _createClass2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime-corejs2/helpers/createClass */ "../node_modules/@babel/runtime-corejs2/helpers/createClass.js"));

var _inherits2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime-corejs2/helpers/inherits */ "../node_modules/@babel/runtime-corejs2/helpers/inherits.js"));

var _createSuper2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime-corejs2/helpers/createSuper */ "../node_modules/@babel/runtime-corejs2/helpers/createSuper.js"));

var _dataTimeFieldBase = _interopRequireDefault(__webpack_require__(/*! ./data-time-field-base */ "../modules/forms/assets/js/frontend/handlers/fields/data-time-field-base.js"));

var TimeField = /*#__PURE__*/function (_FieldBase) {
  (0, _inherits2.default)(TimeField, _FieldBase);

  var _super = (0, _createSuper2.default)(TimeField);

  function TimeField() {
    (0, _classCallCheck2.default)(this, TimeField);
    return _super.apply(this, arguments);
  }

  (0, _createClass2.default)(TimeField, [{
    key: "getFieldsSelector",
    value: function getFieldsSelector() {
      return '.elementor-time-field';
    }
  }, {
    key: "getPickerOptions",
    value: function getPickerOptions() {
      return {
        noCalendar: true,
        enableTime: true,
        allowInput: true
      };
    }
  }]);
  return TimeField;
}(_dataTimeFieldBase.default);

exports.default = TimeField;

/***/ }),

/***/ "../modules/forms/assets/js/frontend/handlers/form-redirect.js":
/*!*********************************************************************!*\
  !*** ../modules/forms/assets/js/frontend/handlers/form-redirect.js ***!
  \*********************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";


var _Object$defineProperty = __webpack_require__(/*! @babel/runtime-corejs2/core-js/object/define-property */ "../node_modules/@babel/runtime-corejs2/core-js/object/define-property.js");

_Object$defineProperty(exports, "__esModule", {
  value: true
});

exports.default = void 0;

__webpack_require__(/*! core-js/modules/es6.array.find */ "../node_modules/core-js/modules/es6.array.find.js");

var _default = elementorModules.frontend.handlers.Base.extend({
  getDefaultSettings: function getDefaultSettings() {
    return {
      selectors: {
        form: '.elementor-form'
      }
    };
  },
  getDefaultElements: function getDefaultElements() {
    var selectors = this.getSettings('selectors'),
        elements = {};
    elements.$form = this.$element.find(selectors.form);
    return elements;
  },
  bindEvents: function bindEvents() {
    this.elements.$form.on('form_destruct', this.handleSubmit);
  },
  handleSubmit: function handleSubmit(event, response) {
    if ('undefined' !== typeof response.data.redirect_url) {
      location.href = response.data.redirect_url;
    }
  }
});

exports.default = _default;

/***/ }),

/***/ "../modules/forms/assets/js/frontend/handlers/form-sender.js":
/*!*******************************************************************!*\
  !*** ../modules/forms/assets/js/frontend/handlers/form-sender.js ***!
  \*******************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime-corejs2/helpers/interopRequireDefault */ "../node_modules/@babel/runtime-corejs2/helpers/interopRequireDefault.js");

var _Object$defineProperty = __webpack_require__(/*! @babel/runtime-corejs2/core-js/object/define-property */ "../node_modules/@babel/runtime-corejs2/core-js/object/define-property.js");

_Object$defineProperty(exports, "__esModule", {
  value: true
});

exports.default = void 0;

__webpack_require__(/*! core-js/modules/es6.regexp.to-string */ "../node_modules/core-js/modules/es6.regexp.to-string.js");

__webpack_require__(/*! core-js/modules/es6.object.to-string */ "../node_modules/core-js/modules/es6.object.to-string.js");

var _parseInt2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime-corejs2/core-js/parse-int */ "../node_modules/@babel/runtime-corejs2/core-js/parse-int.js"));

__webpack_require__(/*! core-js/modules/es6.array.find */ "../node_modules/core-js/modules/es6.array.find.js");

var _default = elementorModules.frontend.handlers.Base.extend({
  getDefaultSettings: function getDefaultSettings() {
    return {
      selectors: {
        form: '.elementor-form',
        submitButton: '[type="submit"]'
      },
      action: 'elementor_pro_forms_send_form',
      ajaxUrl: elementorProFrontend.config.ajaxurl
    };
  },
  getDefaultElements: function getDefaultElements() {
    var selectors = this.getSettings('selectors'),
        elements = {};
    elements.$form = this.$element.find(selectors.form);
    elements.$submitButton = elements.$form.find(selectors.submitButton);
    return elements;
  },
  bindEvents: function bindEvents() {
    this.elements.$form.on('submit', this.handleSubmit);
    var $fileInput = this.elements.$form.find('input[type=file]');

    if ($fileInput.length) {
      $fileInput.on('change', this.validateFileSize);
    }
  },
  validateFileSize: function validateFileSize(event) {
    var _this = this;

    var $field = jQuery(event.currentTarget),
        files = $field[0].files;

    if (!files.length) {
      return;
    }

    var maxSize = (0, _parseInt2.default)($field.attr('data-maxsize')) * 1024 * 1024,
        maxSizeMessage = $field.attr('data-maxsize-message');
    var filesArray = Array.prototype.slice.call(files);
    filesArray.forEach(function (file) {
      if (maxSize < file.size) {
        $field.parent().addClass('elementor-error').append('<span class="elementor-message elementor-message-danger elementor-help-inline elementor-form-help-inline" role="alert">' + maxSizeMessage + '</span>').find(':input').attr('aria-invalid', 'true');

        _this.elements.$form.trigger('error');
      }
    });
  },
  beforeSend: function beforeSend() {
    var $form = this.elements.$form;
    $form.animate({
      opacity: '0.45'
    }, 500).addClass('elementor-form-waiting');
    $form.find('.elementor-message').remove();
    $form.find('.elementor-error').removeClass('elementor-error');
    $form.find('div.elementor-field-group').removeClass('error').find('span.elementor-form-help-inline').remove().end().find(':input').attr('aria-invalid', 'false');
    this.elements.$submitButton.attr('disabled', 'disabled').find('> span').prepend('<span class="elementor-button-text elementor-form-spinner"><i class="fa fa-spinner fa-spin"></i>&nbsp;</span>');
  },
  getFormData: function getFormData() {
    var formData = new FormData(this.elements.$form[0]);
    formData.append('action', this.getSettings('action'));
    formData.append('referrer', location.toString());
    return formData;
  },
  onSuccess: function onSuccess(response) {
    var $form = this.elements.$form;
    this.elements.$submitButton.removeAttr('disabled').find('.elementor-form-spinner').remove();
    $form.animate({
      opacity: '1'
    }, 100).removeClass('elementor-form-waiting');

    if (!response.success) {
      if (response.data.errors) {
        jQuery.each(response.data.errors, function (key, title) {
          $form.find('#form-field-' + key).parent().addClass('elementor-error').append('<span class="elementor-message elementor-message-danger elementor-help-inline elementor-form-help-inline" role="alert">' + title + '</span>').find(':input').attr('aria-invalid', 'true');
        });
        $form.trigger('error');
      }

      $form.append('<div class="elementor-message elementor-message-danger" role="alert">' + response.data.message + '</div>');
    } else {
      $form.trigger('submit_success', response.data); // For actions like redirect page

      $form.trigger('form_destruct', response.data);
      $form.trigger('reset');

      if ('undefined' !== typeof response.data.message && '' !== response.data.message) {
        $form.append('<div class="elementor-message elementor-message-success" role="alert">' + response.data.message + '</div>');
      }
    }
  },
  onError: function onError(xhr, desc) {
    var $form = this.elements.$form;
    $form.append('<div class="elementor-message elementor-message-danger" role="alert">' + desc + '</div>');
    this.elements.$submitButton.html(this.elements.$submitButton.text()).removeAttr('disabled');
    $form.animate({
      opacity: '1'
    }, 100).removeClass('elementor-form-waiting');
    $form.trigger('error');
  },
  handleSubmit: function handleSubmit(event) {
    var self = this,
        $form = this.elements.$form;
    event.preventDefault();

    if ($form.hasClass('elementor-form-waiting')) {
      return false;
    }

    this.beforeSend();
    jQuery.ajax({
      url: self.getSettings('ajaxUrl'),
      type: 'POST',
      dataType: 'json',
      data: self.getFormData(),
      processData: false,
      contentType: false,
      success: self.onSuccess,
      error: self.onError
    });
  }
});

exports.default = _default;

/***/ }),

/***/ "../modules/forms/assets/js/frontend/handlers/form-steps.js":
/*!******************************************************************!*\
  !*** ../modules/forms/assets/js/frontend/handlers/form-steps.js ***!
  \******************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime-corejs2/helpers/interopRequireDefault */ "../node_modules/@babel/runtime-corejs2/helpers/interopRequireDefault.js");

var _Object$defineProperty = __webpack_require__(/*! @babel/runtime-corejs2/core-js/object/define-property */ "../node_modules/@babel/runtime-corejs2/core-js/object/define-property.js");

_Object$defineProperty(exports, "__esModule", {
  value: true
});

exports.default = void 0;

__webpack_require__(/*! core-js/modules/es6.regexp.replace */ "../node_modules/core-js/modules/es6.regexp.replace.js");

__webpack_require__(/*! core-js/modules/es6.regexp.match */ "../node_modules/core-js/modules/es6.regexp.match.js");

__webpack_require__(/*! core-js/modules/es7.array.includes */ "../node_modules/core-js/modules/es7.array.includes.js");

__webpack_require__(/*! core-js/modules/es6.string.includes */ "../node_modules/core-js/modules/es6.string.includes.js");

var _objectSpread2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime-corejs2/helpers/objectSpread2 */ "../node_modules/@babel/runtime-corejs2/helpers/objectSpread2.js"));

__webpack_require__(/*! core-js/modules/es6.array.find */ "../node_modules/core-js/modules/es6.array.find.js");

var _classCallCheck2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime-corejs2/helpers/classCallCheck */ "../node_modules/@babel/runtime-corejs2/helpers/classCallCheck.js"));

var _createClass2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime-corejs2/helpers/createClass */ "../node_modules/@babel/runtime-corejs2/helpers/createClass.js"));

var _get3 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime-corejs2/helpers/get */ "../node_modules/@babel/runtime-corejs2/helpers/get.js"));

var _getPrototypeOf2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime-corejs2/helpers/getPrototypeOf */ "../node_modules/@babel/runtime-corejs2/helpers/getPrototypeOf.js"));

var _inherits2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime-corejs2/helpers/inherits */ "../node_modules/@babel/runtime-corejs2/helpers/inherits.js"));

var _createSuper2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime-corejs2/helpers/createSuper */ "../node_modules/@babel/runtime-corejs2/helpers/createSuper.js"));

var FormSteps = /*#__PURE__*/function (_elementorModules$fro) {
  (0, _inherits2.default)(FormSteps, _elementorModules$fro);

  var _super = (0, _createSuper2.default)(FormSteps);

  function FormSteps() {
    (0, _classCallCheck2.default)(this, FormSteps);
    return _super.apply(this, arguments);
  }

  (0, _createClass2.default)(FormSteps, [{
    key: "getDefaultSettings",
    value: function getDefaultSettings() {
      return {
        selectors: {
          form: '.elementor-form',
          fieldsWrapper: '.elementor-form-fields-wrapper',
          fieldGroup: '.elementor-field-group',
          stepWrapper: '.elementor-field-type-step',
          stepField: '.e-field-step',
          submitWrapper: '.elementor-field-type-submit',
          submitButton: '[type="submit"]',
          buttons: '.e-form__buttons',
          buttonWrapper: '.e-form__buttons__wrapper',
          button: '.e-form__buttons__wrapper__button',
          indicator: '.e-form__indicators__indicator',
          indicatorProgress: '.e-form__indicators__indicator__progress',
          indicatorProgressMeter: '.e-form__indicators__indicator__progress__meter',
          formHelpInline: '.elementor-form-help-inline'
        },
        classes: {
          hidden: 'elementor-hidden',
          column: 'elementor-column',
          fieldGroup: 'elementor-field-group',
          elementorButton: 'elementor-button',
          step: 'e-form__step',
          buttons: 'e-form__buttons',
          buttonWrapper: 'e-form__buttons__wrapper',
          button: 'e-form__buttons__wrapper__button',
          indicators: 'e-form__indicators',
          indicator: 'e-form__indicators__indicator',
          indicatorIcon: 'e-form__indicators__indicator__icon',
          indicatorNumber: 'e-form__indicators__indicator__number',
          indicatorLabel: 'e-form__indicators__indicator__label',
          indicatorProgress: 'e-form__indicators__indicator__progress',
          indicatorProgressMeter: 'e-form__indicators__indicator__progress__meter',
          indicatorSeparator: 'e-form__indicators__indicator__separator',
          indicatorInactive: 'e-form__indicators__indicator--state-inactive',
          indicatorActive: 'e-form__indicators__indicator--state-active',
          indicatorCompleted: 'e-form__indicators__indicator--state-completed',
          indicatorShapeCircle: 'e-form__indicators__indicator--shape-circle',
          indicatorShapeSquare: 'e-form__indicators__indicator--shape-square',
          indicatorShapeRounded: 'e-form__indicators__indicator--shape-rounded',
          indicatorShapeNone: 'e-form__indicators__indicator--shape-none'
        }
      };
    }
  }, {
    key: "getDefaultElements",
    value: function getDefaultElements() {
      var _this$getSettings = this.getSettings(),
          selectors = _this$getSettings.selectors,
          elements = {
        $form: this.$element.find(selectors.form)
      };

      elements.$fieldsWrapper = elements.$form.children(selectors.fieldsWrapper);
      elements.$stepWrapper = elements.$fieldsWrapper.children(selectors.stepWrapper);
      elements.$stepField = elements.$stepWrapper.children(selectors.stepField);
      elements.$fieldGroup = elements.$fieldsWrapper.children(selectors.fieldGroup);
      elements.$submitWrapper = elements.$fieldsWrapper.children(selectors.submitWrapper);
      elements.$submitButton = elements.$submitWrapper.children(selectors.submitButton);
      return elements;
    }
  }, {
    key: "onInit",
    value: function onInit() {
      var _get2;

      for (var _len = arguments.length, args = new Array(_len), _key = 0; _key < _len; _key++) {
        args[_key] = arguments[_key];
      }

      (_get2 = (0, _get3.default)((0, _getPrototypeOf2.default)(FormSteps.prototype), "onInit", this)).call.apply(_get2, [this].concat(args));

      if (!this.isStepsExist()) {
        return;
      }

      this.data = {
        steps: []
      };
      this.state = {
        currentStep: 0,
        stepsType: '',
        stepsShape: ''
      };
      this.buildSteps();
      this.elements = (0, _objectSpread2.default)((0, _objectSpread2.default)((0, _objectSpread2.default)({}, this.elements), this.createStepsIndicators()), this.createStepsButtons());
      this.initProgressBar();
      this.extractResponsiveSizeFromSubmitWrapper();
    }
  }, {
    key: "bindEvents",
    value: function bindEvents() {
      var _this = this;

      if (!this.isStepsExist()) {
        return;
      }

      this.elements.$form.on({
        submit: function submit() {
          return _this.resetForm();
        },
        keydown: function keydown(e) {
          if (13 === e.keyCode && !_this.isLastStep() && 'textarea' !== e.target.localName) {
            e.preventDefault();

            _this.applyStep('next');
          }
        },
        error: function error() {
          return _this.onFormError();
        }
      });
    }
  }, {
    key: "isStepsExist",
    value: function isStepsExist() {
      return this.elements.$stepWrapper.length;
    }
  }, {
    key: "initProgressBar",
    value: function initProgressBar() {
      var stepsSettings = this.getElementSettings();

      if ('progress_bar' === stepsSettings.step_type) {
        this.setProgressBar();
      }
    }
  }, {
    key: "buildSteps",
    value: function buildSteps() {
      var _this2 = this;

      this.elements.$stepWrapper.each(function (index, el) {
        var _this2$getSettings = _this2.getSettings(),
            selectors = _this2$getSettings.selectors,
            classes = _this2$getSettings.classes,
            $currentStep = jQuery(el);

        $currentStep.addClass(classes.step).removeClass(classes.fieldGroup, classes.column);

        if (index) {
          $currentStep.addClass(classes.hidden);
        }

        _this2.setStepData($currentStep.children(selectors.stepField));

        $currentStep.append($currentStep.nextUntil(_this2.elements.$stepWrapper).not(_this2.elements.$submitWrapper));
      });
    }
  }, {
    key: "setStepData",
    value: function setStepData($stepElement) {
      var dataAttributes = ['label', 'previousButton', 'nextButton', 'iconUrl', 'iconLibrary'],
          stepData = {};
      dataAttributes.forEach(function (attr) {
        var attrValue = $stepElement.attr('data-' + attr);

        if (attrValue) {
          stepData[attr] = attrValue;
        }
      });
      this.data.steps.push(stepData);
    }
  }, {
    key: "createStepsIndicators",
    value: function createStepsIndicators() {
      var stepsSettings = this.getElementSettings(),
          stepsElements = {};

      if ('none' !== stepsSettings.step_type) {
        var _this$getSettings2 = this.getSettings(),
            selectors = _this$getSettings2.selectors,
            classes = _this$getSettings2.classes,
            indicatorsTypeClass = classes.indicators + '--type-' + stepsSettings.step_type,
            indicatorsClasses = [classes.indicators, indicatorsTypeClass];

        stepsElements.$indicatorsWrapper = jQuery('<div>', {
          class: indicatorsClasses.join(' ')
        });
        stepsElements.$indicatorsWrapper.append(this.buildIndicators());
        this.elements.$fieldsWrapper.before(stepsElements.$indicatorsWrapper);

        if ('progress_bar' === stepsSettings.step_type) {
          stepsElements.$progressBar = stepsElements.$indicatorsWrapper.find(selectors.indicatorProgress);
          stepsElements.$progressBarMeter = stepsElements.$indicatorsWrapper.find(selectors.indicatorProgressMeter);
        } else {
          stepsElements.$indicators = stepsElements.$indicatorsWrapper.find(selectors.indicator);
          stepsElements.$currentIndicator = stepsElements.$indicators.eq(this.state.currentStep);
        }
      }

      this.saveIndicatorsState();
      return stepsElements;
    }
  }, {
    key: "buildIndicators",
    value: function buildIndicators() {
      var stepsSettings = this.getElementSettings();
      return 'progress_bar' === stepsSettings.step_type ? this.buildProgressBar() : this.buildIndicatorsFromStepsData();
    }
  }, {
    key: "buildProgressBar",
    value: function buildProgressBar() {
      var _this$getSettings3 = this.getSettings(),
          classes = _this$getSettings3.classes,
          $progressBar = jQuery('<div>', {
        class: classes.indicatorProgress
      }),
          $progressBarMeter = jQuery('<div>', {
        class: classes.indicatorProgressMeter
      });

      $progressBar.append($progressBarMeter);
      return $progressBar;
    }
  }, {
    key: "getProgressBarValue",
    value: function getProgressBarValue() {
      var totalSteps = this.data.steps.length,
          currentStep = this.state.currentStep,
          percentage = currentStep ? (currentStep + 1) / totalSteps * 100 : 100 / totalSteps;
      return Math.floor(percentage) + '%';
    }
  }, {
    key: "setProgressBar",
    value: function setProgressBar() {
      var progressBarValue = this.getProgressBarValue();
      this.updateProgressMeterCSSVariable(progressBarValue);
      this.elements.$progressBarMeter.text(progressBarValue);
    }
  }, {
    key: "updateProgressMeterCSSVariable",
    value: function updateProgressMeterCSSVariable(value) {
      this.$element[0].style.setProperty('--e-form-steps-indicator-progress-meter-width', value);
    }
  }, {
    key: "saveIndicatorsState",
    value: function saveIndicatorsState() {
      var stepsSettings = this.getElementSettings();
      this.state.stepsType = stepsSettings.step_type;

      if (!['none', 'text', 'progress_bar'].includes(stepsSettings.step_type)) {
        this.state.stepsShape = stepsSettings.step_icon_shape;
      }
    }
  }, {
    key: "buildIndicatorsFromStepsData",
    value: function buildIndicatorsFromStepsData() {
      var _this3 = this;

      var indicators = [];
      this.data.steps.forEach(function (stepObj, index) {
        if (index) {
          indicators.push(_this3.getStepSeparator());
        }

        indicators.push(_this3.getStepIndicatorElement(stepObj, index));
      });
      return indicators;
    }
  }, {
    key: "getStepIndicatorElement",
    value: function getStepIndicatorElement(stepObj, index) {
      var _this$getSettings4 = this.getSettings(),
          classes = _this$getSettings4.classes,
          stepsSettings = this.getElementSettings(),
          indicatorStateClass = this.getIndicatorStateClass(index),
          indicatorClasses = [classes.indicator, indicatorStateClass],
          $stepIndicator = jQuery('<div>', {
        class: indicatorClasses.join(' ')
      });

      if (stepsSettings.step_type.includes('icon')) {
        $stepIndicator.append(this.getStepIconElement(stepObj));
      }

      if (stepsSettings.step_type.includes('number')) {
        $stepIndicator.append(this.getStepNumberElement(index));
      }

      if (stepsSettings.step_type.includes('text')) {
        $stepIndicator.append(this.getStepLabelElement(stepObj.label));
      }

      return $stepIndicator;
    }
  }, {
    key: "getIndicatorStateClass",
    value: function getIndicatorStateClass(index) {
      var _this$getSettings5 = this.getSettings(),
          classes = _this$getSettings5.classes;

      if (index < this.state.currentStep) {
        return classes.indicatorCompleted;
      } else if (index > this.state.currentStep) {
        return classes.indicatorInactive;
      }

      return classes.indicatorActive;
    }
  }, {
    key: "getIndicatorShapeClass",
    value: function getIndicatorShapeClass() {
      var stepsSettings = this.getElementSettings(),
          _this$getSettings6 = this.getSettings(),
          classes = _this$getSettings6.classes;

      return classes['indicatorShape' + this.firstLetterToUppercase(stepsSettings.step_icon_shape)];
    }
  }, {
    key: "firstLetterToUppercase",
    value: function firstLetterToUppercase(str) {
      return str.charAt(0).toUpperCase() + str.slice(1);
    }
  }, {
    key: "getStepNumberElement",
    value: function getStepNumberElement(index) {
      var _this$getSettings7 = this.getSettings(),
          classes = _this$getSettings7.classes,
          numberClasses = [classes.indicatorNumber, this.getIndicatorShapeClass()];

      return jQuery('<div>', {
        class: numberClasses.join(' '),
        text: index + 1
      });
    }
  }, {
    key: "getStepIconElement",
    value: function getStepIconElement(stepObj) {
      var _this$getSettings8 = this.getSettings(),
          classes = _this$getSettings8.classes,
          iconClasses = [classes.indicatorIcon, this.getIndicatorShapeClass()],
          $icon = jQuery('<div>', {
        class: iconClasses.join(' ')
      }),
          iconType = stepObj.iconLibrary ? '<i>' : '<img>',
          iconAttrObj = stepObj.iconLibrary ? {
        class: stepObj.iconLibrary
      } : {
        src: stepObj.iconUrl
      };

      $icon.append(jQuery(iconType, iconAttrObj));
      return $icon;
    }
  }, {
    key: "getStepLabelElement",
    value: function getStepLabelElement(label) {
      var _this$getSettings9 = this.getSettings(),
          classes = _this$getSettings9.classes;

      return jQuery('<label>', {
        class: classes.indicatorLabel,
        text: label
      });
    }
  }, {
    key: "getStepSeparator",
    value: function getStepSeparator() {
      var _this$getSettings10 = this.getSettings(),
          classes = _this$getSettings10.classes;

      return jQuery('<div>', {
        class: classes.indicatorSeparator
      });
    }
  }, {
    key: "createStepsButtons",
    value: function createStepsButtons() {
      var _this$getSettings11 = this.getSettings(),
          selectors = _this$getSettings11.selectors,
          stepsElements = {};

      this.injectButtonsToSteps(stepsElements);
      stepsElements.$buttonsContainer = this.elements.$stepWrapper.find(selectors.buttons);
      stepsElements.$buttonsWrappers = stepsElements.$buttonsContainer.children(selectors.buttonWrapper);
      return stepsElements;
    }
  }, {
    key: "injectButtonsToSteps",
    value: function injectButtonsToSteps() {
      var _this4 = this;

      var totalSteps = this.elements.$stepWrapper.length;
      this.elements.$stepWrapper.each(function (index, el) {
        var $el = jQuery(el),
            $container = _this4.getButtonsContainer();

        var $nextButton;

        if (index) {
          $container.append(_this4.getStepButton('previous', index));
          $nextButton = index === totalSteps - 1 ? _this4.getSubmitButton() : _this4.getStepButton('next', index);
        } else {
          $nextButton = _this4.getStepButton('next', index);
        }

        $container.append($nextButton);
        $el.append($container);
      });
    }
  }, {
    key: "getButtonsContainer",
    value: function getButtonsContainer() {
      var _this$getSettings12 = this.getSettings(),
          classes = _this$getSettings12.classes,
          stepsSettings = this.getElementSettings(),
          buttonColumnWidthClasses = [classes.buttons, classes.column, 'elementor-col-' + stepsSettings.button_width];

      return jQuery('<div>', {
        class: buttonColumnWidthClasses.join(' ')
      });
    }
  }, {
    key: "extractResponsiveSizeFromSubmitWrapper",
    value: function extractResponsiveSizeFromSubmitWrapper() {
      var sizeClasses = [];
      this.elements.$submitWrapper.removeClass(function (index, className) {
        var _className$match;

        sizeClasses = (_className$match = className.match(/elementor-(sm|md)-[0-9]+/g)) === null || _className$match === void 0 ? void 0 : _className$match.join(' ');
        return sizeClasses;
      });
      this.elements.$buttonsContainer.addClass(sizeClasses);
    }
  }, {
    key: "getStepButton",
    value: function getStepButton(buttonType, index) {
      var _this5 = this;

      var _this$getSettings13 = this.getSettings(),
          classes = _this$getSettings13.classes,
          $button = this.getButton(buttonType, index).on('click', function () {
        return _this5.applyStep(buttonType);
      }),
          buttonWrapperClasses = [classes.fieldGroup, classes.buttonWrapper, 'elementor-field-type-' + buttonType];

      return jQuery('<div>', {
        class: buttonWrapperClasses.join(' ')
      }).append($button);
    }
  }, {
    key: "getSubmitButton",
    value: function getSubmitButton() {
      var _this6 = this;

      var _this$getSettings14 = this.getSettings(),
          classes = _this$getSettings14.classes;

      this.elements.$submitButton.addClass(classes.button); // TODO: When a solution for the conditions will be found, check if can remove the elementor-col-x manipulation.

      return this.elements.$submitWrapper.attr('class', function (index, className) {
        return _this6.replaceClassNameColSize(className, '');
      }).removeClass(classes.column).removeClass(classes.buttons).addClass(classes.buttonWrapper);
    }
  }, {
    key: "replaceClassNameColSize",
    value: function replaceClassNameColSize(className, value) {
      return className.replace(/elementor-col-([0-9]+)/g, value);
    }
  }, {
    key: "getButton",
    value: function getButton(buttonType, index) {
      var _this$getSettings15 = this.getSettings(),
          classes = _this$getSettings15.classes,
          submitSizeClass = this.elements.$submitButton.attr('class').match(/elementor-size-([^\W\d]+)/g),
          buttonClasses = [classes.elementorButton, submitSizeClass, classes.button, classes.button + '-' + buttonType];

      return jQuery('<button>', {
        type: 'button',
        text: this.getButtonLabel(buttonType, index),
        class: buttonClasses.join(' ')
      });
    }
  }, {
    key: "getButtonLabel",
    value: function getButtonLabel(buttonType, index) {
      var stepsSettings = this.getElementSettings(),
          stepData = this.data.steps[index],
          buttonName = buttonType + 'Button',
          buttonSettingsProp = "step_".concat(buttonType, "_label");
      return stepData[buttonName] || stepsSettings[buttonSettingsProp];
    }
  }, {
    key: "applyStep",
    value: function applyStep(direction) {
      var nextIndex = 'next' === direction ? this.state.currentStep + 1 : this.state.currentStep - 1;

      if ('next' === direction && !this.isFieldsValid(this.elements.$stepWrapper)) {
        return false;
      }

      this.goToStep(nextIndex);
      this.state.currentStep = nextIndex;

      if ('progress_bar' === this.state.stepsType) {
        this.setProgressBar();
      } else if ('none' !== this.state.stepsType) {
        this.updateIndicatorsState(direction);
      }
    }
  }, {
    key: "goToStep",
    value: function goToStep(index) {
      var _this$getSettings16 = this.getSettings(),
          classes = _this$getSettings16.classes;

      this.elements.$stepWrapper.eq(this.state.currentStep).addClass(classes.hidden);
      this.elements.$stepWrapper.eq(index).removeClass(classes.hidden).children(this.getSettings('selectors.fieldGroup')).first().find(':input').first().trigger('focus');
    }
  }, {
    key: "isFieldsValid",
    value: function isFieldsValid($stepWrapper) {
      var isValid = true;
      $stepWrapper.eq(this.state.currentStep).find('.elementor-field-group :input').each(function (index, el) {
        if (!el.checkValidity()) {
          el.reportValidity();
          return isValid = false;
        }
      });
      return isValid;
    }
  }, {
    key: "isLastStep",
    value: function isLastStep() {
      return this.state.currentStep === this.data.steps.length - 1;
    }
  }, {
    key: "resetForm",
    value: function resetForm() {
      this.state.currentStep = 0;
      this.resetSteps();

      if ('progress_bar' === this.state.stepsType) {
        this.setProgressBar();
      } else if ('none' !== this.state.stepsType) {
        this.elements.$currentIndicator = this.elements.$indicators.eq(this.state.currentStep);
        this.resetIndicators();
      }
    }
  }, {
    key: "resetSteps",
    value: function resetSteps() {
      var _this$getSettings17 = this.getSettings(),
          classes = _this$getSettings17.classes;

      this.elements.$stepWrapper.addClass(classes.hidden).eq(0).removeClass(classes.hidden);
    }
  }, {
    key: "resetIndicators",
    value: function resetIndicators() {
      var _this$getSettings18 = this.getSettings(),
          classes = _this$getSettings18.classes,
          stateTypes = ['inactive', 'active', 'completed'],
          stateClasses = stateTypes.map(function (state) {
        return classes.indicator + '--state-' + state;
      });

      this.elements.$indicators.removeClass(stateClasses.join(' ')).not(this.elements.$indicators.eq(0)).addClass(classes.indicatorInactive);
      this.elements.$indicators.eq(0).addClass(classes.indicatorActive);
    }
  }, {
    key: "updateIndicatorsState",
    value: function updateIndicatorsState(direction) {
      var _this$getSettings19 = this.getSettings(),
          classes = _this$getSettings19.classes,
          indicatorsClasses = {
        current: {
          remove: classes.indicatorActive,
          add: 'next' === direction ? classes.indicatorCompleted : classes.indicatorInactive
        },
        next: {
          remove: 'next' === direction ? classes.indicatorInactive : classes.indicatorCompleted,
          add: classes.indicatorActive
        }
      };

      this.elements.$currentIndicator.removeClass(indicatorsClasses.current.remove).addClass(indicatorsClasses.current.add);
      this.elements.$currentIndicator = this.elements.$indicators.eq(this.state.currentStep);
      this.elements.$currentIndicator.removeClass(indicatorsClasses.next.remove).addClass(indicatorsClasses.next.add);
    }
  }, {
    key: "updateValue",
    value: function updateValue(updatedValue) {
      var _this7 = this;

      var actionsMap = {
        step_type: function step_type() {
          return _this7.updateStepsType();
        },
        step_icon_shape: function step_icon_shape() {
          return _this7.updateStepsShape();
        },
        step_next_label: function step_next_label() {
          return _this7.updateStepButtonsLabel('next');
        },
        step_previous_label: function step_previous_label() {
          return _this7.updateStepButtonsLabel('previous');
        }
      };

      if (actionsMap[updatedValue]) {
        actionsMap[updatedValue]();
      }
    }
  }, {
    key: "updateStepsType",
    value: function updateStepsType() {
      var stepsSettings = this.getElementSettings();

      if (this.elements.$indicatorsWrapper) {
        this.elements.$indicatorsWrapper.remove();
      }

      if ('none' !== stepsSettings.step_type) {
        this.rebuildIndicators();
      }

      this.state.stepsType = stepsSettings.step_type;
    }
  }, {
    key: "rebuildIndicators",
    value: function rebuildIndicators() {
      this.elements = (0, _objectSpread2.default)((0, _objectSpread2.default)({}, this.elements), this.createStepsIndicators());
      this.initProgressBar();
    }
  }, {
    key: "updateStepsShape",
    value: function updateStepsShape() {
      var stepsSettings = this.getElementSettings(),
          _this$getSettings20 = this.getSettings(),
          selectors = _this$getSettings20.selectors,
          classes = _this$getSettings20.classes,
          shapeClassStart = classes.indicator + '--shape-',
          currentShapeClass = shapeClassStart + this.state.stepsShape,
          newShapeClass = shapeClassStart + stepsSettings.step_icon_shape;

      var elementsTargetType = '';

      if (stepsSettings.step_type.includes('icon')) {
        elementsTargetType = 'icon';
      } else if (stepsSettings.step_type.includes('number')) {
        elementsTargetType = 'number';
      }

      this.elements.$indicators.children(selectors.indicator + '__' + elementsTargetType).removeClass(currentShapeClass).addClass(newShapeClass);
      this.state.stepsShape = stepsSettings.step_icon_shape;
    }
  }, {
    key: "updateStepButtonsLabel",
    value: function updateStepButtonsLabel(buttonType) {
      var _this8 = this;

      var _this$getSettings21 = this.getSettings(),
          selectors = _this$getSettings21.selectors,
          buttonSelector = {
        previous: selectors.button + '-previous',
        next: selectors.button + '-next'
      };

      this.elements.$stepWrapper.each(function (index, el) {
        jQuery(el).find(buttonSelector[buttonType]).text(_this8.getButtonLabel(buttonType, index));
      });
    }
  }, {
    key: "onFormError",
    value: function onFormError() {
      var _this$getSettings22 = this.getSettings(),
          selectors = _this$getSettings22.selectors,
          $errorStepElement = this.elements.$form.find(selectors.formHelpInline).closest(selectors.stepWrapper);

      if ($errorStepElement.length) {
        this.goToStep($errorStepElement.index());
      }
    }
  }, {
    key: "onElementChange",
    value: function onElementChange(updatedValue) {
      if (!this.isStepsExist()) {
        return;
      }

      this.updateValue(updatedValue);
    }
  }]);
  return FormSteps;
}(elementorModules.frontend.handlers.Base);

exports.default = FormSteps;

/***/ }),

/***/ "../modules/forms/assets/js/frontend/handlers/recaptcha.js":
/*!*****************************************************************!*\
  !*** ../modules/forms/assets/js/frontend/handlers/recaptcha.js ***!
  \*****************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime-corejs2/helpers/interopRequireDefault */ "../node_modules/@babel/runtime-corejs2/helpers/interopRequireDefault.js");

var _Object$defineProperty = __webpack_require__(/*! @babel/runtime-corejs2/core-js/object/define-property */ "../node_modules/@babel/runtime-corejs2/core-js/object/define-property.js");

_Object$defineProperty(exports, "__esModule", {
  value: true
});

exports.default = void 0;

__webpack_require__(/*! core-js/modules/es6.array.find */ "../node_modules/core-js/modules/es6.array.find.js");

var _classCallCheck2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime-corejs2/helpers/classCallCheck */ "../node_modules/@babel/runtime-corejs2/helpers/classCallCheck.js"));

var _createClass2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime-corejs2/helpers/createClass */ "../node_modules/@babel/runtime-corejs2/helpers/createClass.js"));

var _inherits2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime-corejs2/helpers/inherits */ "../node_modules/@babel/runtime-corejs2/helpers/inherits.js"));

var _createSuper2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime-corejs2/helpers/createSuper */ "../node_modules/@babel/runtime-corejs2/helpers/createSuper.js"));

var Recaptcha = /*#__PURE__*/function (_elementorModules$fro) {
  (0, _inherits2.default)(Recaptcha, _elementorModules$fro);

  var _super = (0, _createSuper2.default)(Recaptcha);

  function Recaptcha() {
    (0, _classCallCheck2.default)(this, Recaptcha);
    return _super.apply(this, arguments);
  }

  (0, _createClass2.default)(Recaptcha, [{
    key: "getDefaultSettings",
    value: function getDefaultSettings() {
      return {
        selectors: {
          recaptcha: '.elementor-g-recaptcha:last',
          submit: 'button[type="submit"]',
          recaptchaResponse: '[name="g-recaptcha-response"]'
        }
      };
    }
  }, {
    key: "getDefaultElements",
    value: function getDefaultElements() {
      var _this$getDefaultSetti = this.getDefaultSettings(),
          selectors = _this$getDefaultSetti.selectors,
          elements = {
        $recaptcha: this.$element.find(selectors.recaptcha)
      };

      elements.$form = elements.$recaptcha.parents('form');
      elements.$submit = elements.$form.find(selectors.submit);
      return elements;
    }
  }, {
    key: "bindEvents",
    value: function bindEvents() {
      this.onRecaptchaApiReady();
    }
  }, {
    key: "isActive",
    value: function isActive(settings) {
      var _this$getDefaultSetti2 = this.getDefaultSettings(),
          selectors = _this$getDefaultSetti2.selectors;

      return settings.$element.find(selectors.recaptcha).length;
    }
  }, {
    key: "addRecaptcha",
    value: function addRecaptcha() {
      var _this = this;

      var settings = this.elements.$recaptcha.data(),
          isV2 = 'v3' !== settings.type,
          captchaIds = [];
      captchaIds.forEach(function (id) {
        return window.grecaptcha.reset(id);
      });
      var widgetId = window.grecaptcha.render(this.elements.$recaptcha[0], settings);
      this.elements.$form.on('reset error', function () {
        window.grecaptcha.reset(widgetId);
      });

      if (isV2) {
        this.elements.$recaptcha.data('widgetId', widgetId);
      } else {
        captchaIds.push(widgetId);
        this.elements.$submit.on('click', function (e) {
          return _this.onV3FormSubmit(e, widgetId);
        });
      }
    }
  }, {
    key: "onV3FormSubmit",
    value: function onV3FormSubmit(e, widgetId) {
      var _this2 = this;

      e.preventDefault();
      window.grecaptcha.ready(function () {
        var $form = _this2.elements.$form;
        grecaptcha.execute(widgetId, {
          action: _this2.elements.$recaptcha.data('action')
        }).then(function (token) {
          if (_this2.elements.$recaptchaResponse) {
            _this2.elements.$recaptchaResponse.val(token);
          } else {
            _this2.elements.$recaptchaResponse = jQuery('<input>', {
              type: 'hidden',
              value: token,
              name: 'g-recaptcha-response'
            });
            $form.append(_this2.elements.$recaptchaResponse);
          }

          $form.trigger('submit');
        });
      });
    }
  }, {
    key: "onRecaptchaApiReady",
    value: function onRecaptchaApiReady() {
      var _this3 = this;

      if (window.grecaptcha && window.grecaptcha.render) {
        this.addRecaptcha();
      } else {
        // If not ready check again by timeout..
        setTimeout(function () {
          return _this3.onRecaptchaApiReady();
        }, 350);
      }
    }
  }]);
  return Recaptcha;
}(elementorModules.frontend.handlers.Base);

exports.default = Recaptcha;

/***/ })

}]);
//# sourceMappingURL=form.da3668d4c18348da0262.bundle.js.map