/*! elementor - v3.2.4 - 26-05-2021 */
(self["webpackChunkelementor"] = self["webpackChunkelementor"] || []).push([["tabs"],{

/***/ "../assets/dev/js/frontend/handlers/tabs.js":
/*!**************************************************!*\
  !*** ../assets/dev/js/frontend/handlers/tabs.js ***!
  \**************************************************/
/*! unknown exports (runtime-defined) */
/*! runtime requirements: __webpack_exports__, __webpack_require__ */
/*! CommonJS bailout: exports is used directly at 7:23-30 */
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";


var _interopRequireDefault = __webpack_require__(/*! @babel/runtime-corejs2/helpers/interopRequireDefault */ "../node_modules/@babel/runtime-corejs2/helpers/interopRequireDefault.js");

var _Object$defineProperty = __webpack_require__(/*! @babel/runtime-corejs2/core-js/object/define-property */ "../node_modules/@babel/runtime-corejs2/core-js/object/define-property.js");

_Object$defineProperty(exports, "__esModule", {
  value: true
});

exports.default = void 0;

var _objectSpread2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime-corejs2/helpers/objectSpread2 */ "../node_modules/@babel/runtime-corejs2/helpers/objectSpread2.js"));

var _classCallCheck2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime-corejs2/helpers/classCallCheck */ "../node_modules/@babel/runtime-corejs2/helpers/classCallCheck.js"));

var _createClass2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime-corejs2/helpers/createClass */ "../node_modules/@babel/runtime-corejs2/helpers/createClass.js"));

var _get2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime-corejs2/helpers/get */ "../node_modules/@babel/runtime-corejs2/helpers/get.js"));

var _getPrototypeOf2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime-corejs2/helpers/getPrototypeOf */ "../node_modules/@babel/runtime-corejs2/helpers/getPrototypeOf.js"));

var _inherits2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime-corejs2/helpers/inherits */ "../node_modules/@babel/runtime-corejs2/helpers/inherits.js"));

var _createSuper2 = _interopRequireDefault(__webpack_require__(/*! @babel/runtime-corejs2/helpers/createSuper */ "../node_modules/@babel/runtime-corejs2/helpers/createSuper.js"));

var _baseTabs = _interopRequireDefault(__webpack_require__(/*! ./base-tabs */ "../assets/dev/js/frontend/handlers/base-tabs.js"));

var Tabs = /*#__PURE__*/function (_TabsModule) {
  (0, _inherits2.default)(Tabs, _TabsModule);

  var _super = (0, _createSuper2.default)(Tabs);

  function Tabs() {
    (0, _classCallCheck2.default)(this, Tabs);
    return _super.apply(this, arguments);
  }

  (0, _createClass2.default)(Tabs, [{
    key: "getDefaultSettings",
    value: function getDefaultSettings() {
      var defaultSettings = (0, _get2.default)((0, _getPrototypeOf2.default)(Tabs.prototype), "getDefaultSettings", this).call(this);
      return (0, _objectSpread2.default)((0, _objectSpread2.default)({}, defaultSettings), {}, {
        toggleSelf: false
      });
    }
  }]);
  return Tabs;
}(_baseTabs.default);

exports.default = Tabs;

/***/ })

}]);
//# sourceMappingURL=tabs.e9db15b9aa8a58bcf375.bundle.js.map