/******/ (function(modules) { // webpackBootstrap
/******/ 	// The module cache
/******/ 	var installedModules = {};
/******/
/******/ 	// The require function
/******/ 	function __webpack_require__(moduleId) {
/******/
/******/ 		// Check if module is in cache
/******/ 		if(installedModules[moduleId]) {
/******/ 			return installedModules[moduleId].exports;
/******/ 		}
/******/ 		// Create a new module (and put it into the cache)
/******/ 		var module = installedModules[moduleId] = {
/******/ 			i: moduleId,
/******/ 			l: false,
/******/ 			exports: {}
/******/ 		};
/******/
/******/ 		// Execute the module function
/******/ 		modules[moduleId].call(module.exports, module, module.exports, __webpack_require__);
/******/
/******/ 		// Flag the module as loaded
/******/ 		module.l = true;
/******/
/******/ 		// Return the exports of the module
/******/ 		return module.exports;
/******/ 	}
/******/
/******/
/******/ 	// expose the modules object (__webpack_modules__)
/******/ 	__webpack_require__.m = modules;
/******/
/******/ 	// expose the module cache
/******/ 	__webpack_require__.c = installedModules;
/******/
/******/ 	// define getter function for harmony exports
/******/ 	__webpack_require__.d = function(exports, name, getter) {
/******/ 		if(!__webpack_require__.o(exports, name)) {
/******/ 			Object.defineProperty(exports, name, { enumerable: true, get: getter });
/******/ 		}
/******/ 	};
/******/
/******/ 	// define __esModule on exports
/******/ 	__webpack_require__.r = function(exports) {
/******/ 		if(typeof Symbol !== 'undefined' && Symbol.toStringTag) {
/******/ 			Object.defineProperty(exports, Symbol.toStringTag, { value: 'Module' });
/******/ 		}
/******/ 		Object.defineProperty(exports, '__esModule', { value: true });
/******/ 	};
/******/
/******/ 	// create a fake namespace object
/******/ 	// mode & 1: value is a module id, require it
/******/ 	// mode & 2: merge all properties of value into the ns
/******/ 	// mode & 4: return value when already ns object
/******/ 	// mode & 8|1: behave like require
/******/ 	__webpack_require__.t = function(value, mode) {
/******/ 		if(mode & 1) value = __webpack_require__(value);
/******/ 		if(mode & 8) return value;
/******/ 		if((mode & 4) && typeof value === 'object' && value && value.__esModule) return value;
/******/ 		var ns = Object.create(null);
/******/ 		__webpack_require__.r(ns);
/******/ 		Object.defineProperty(ns, 'default', { enumerable: true, value: value });
/******/ 		if(mode & 2 && typeof value != 'string') for(var key in value) __webpack_require__.d(ns, key, function(key) { return value[key]; }.bind(null, key));
/******/ 		return ns;
/******/ 	};
/******/
/******/ 	// getDefaultExport function for compatibility with non-harmony modules
/******/ 	__webpack_require__.n = function(module) {
/******/ 		var getter = module && module.__esModule ?
/******/ 			function getDefault() { return module['default']; } :
/******/ 			function getModuleExports() { return module; };
/******/ 		__webpack_require__.d(getter, 'a', getter);
/******/ 		return getter;
/******/ 	};
/******/
/******/ 	// Object.prototype.hasOwnProperty.call
/******/ 	__webpack_require__.o = function(object, property) { return Object.prototype.hasOwnProperty.call(object, property); };
/******/
/******/ 	// __webpack_public_path__
/******/ 	__webpack_require__.p = "/";
/******/
/******/
/******/ 	// Load entry module and return exports
/******/ 	return __webpack_require__(__webpack_require__.s = 0);
/******/ })
/************************************************************************/
/******/ ({

/***/ "./src/web/assets/editevent/src/EventTypeSwitcher.js":
/*!***********************************************************!*\
  !*** ./src/web/assets/editevent/src/EventTypeSwitcher.js ***!
  \***********************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

(function ($) {
  /** global: Craft */

  /** global: Garnish */
  Craft.EventTypeSwitcher = Garnish.Base.extend({
    $typeSelect: null,
    $spinner: null,
    init: function init() {
      this.$typeSelect = $('#typeId');
      this.$spinner = $('<div class="spinner hidden"/>').insertAfter(this.$typeSelect.parent());
      this.addListener(this.$typeSelect, 'change', 'onTypeChange');
    },
    onTypeChange: function onTypeChange(ev) {
      this.$spinner.removeClass('hidden');
      Craft.postActionRequest('eventsky/events/switch-event-type', Craft.cp.$primaryForm.serialize(), $.proxy(function (response, textStatus) {
        this.$spinner.addClass('hidden');

        if (textStatus === 'success') {
          this.trigger('beforeTypeChange');
          var $tabs = $('#tabs');

          if ($tabs.length) {
            $tabs.replaceWith(response.tabsHtml);
          } else {
            $(response.tabsHtml).insertBefore($('#content'));
          }

          $('#fields').html(response.fieldsHtml);
          Craft.initUiElements($('#fields'));
          Craft.appendHeadHtml(response.headHtml);
          Craft.appendFootHtml(response.bodyHtml); // Update the slug generator with the new title input

          if (typeof slugGenerator !== 'undefined') {
            slugGenerator.setNewSource('#title');
          }

          Craft.cp.initTabs();
          this.trigger('typeChange');
        }
      }, this));
    }
  });
})(jQuery);

/***/ }),

/***/ "./src/web/assets/editevent/src/index.js":
/*!***********************************************!*\
  !*** ./src/web/assets/editevent/src/index.js ***!
  \***********************************************/
/*! no exports provided */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _EventTypeSwitcher__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./EventTypeSwitcher */ "./src/web/assets/editevent/src/EventTypeSwitcher.js");
/* harmony import */ var _EventTypeSwitcher__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_EventTypeSwitcher__WEBPACK_IMPORTED_MODULE_0__);


/***/ }),

/***/ "./src/web/assets/editevent/src/style.scss":
/*!*************************************************!*\
  !*** ./src/web/assets/editevent/src/style.scss ***!
  \*************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

// removed by extract-text-webpack-plugin

/***/ }),

/***/ "./src/web/assets/editticket/src/style.scss":
/*!**************************************************!*\
  !*** ./src/web/assets/editticket/src/style.scss ***!
  \**************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

// removed by extract-text-webpack-plugin

/***/ }),

/***/ 0:
/*!******************************************************************************************************************************************!*\
  !*** multi ./src/web/assets/editevent/src/index.js ./src/web/assets/editevent/src/style.scss ./src/web/assets/editticket/src/style.scss ***!
  \******************************************************************************************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

__webpack_require__(/*! /Users/sarah/Projects/CraftPlugins/EventPlugin/FREDMANSKY_eventplugin/src/web/assets/editevent/src/index.js */"./src/web/assets/editevent/src/index.js");
__webpack_require__(/*! /Users/sarah/Projects/CraftPlugins/EventPlugin/FREDMANSKY_eventplugin/src/web/assets/editevent/src/style.scss */"./src/web/assets/editevent/src/style.scss");
module.exports = __webpack_require__(/*! /Users/sarah/Projects/CraftPlugins/EventPlugin/FREDMANSKY_eventplugin/src/web/assets/editticket/src/style.scss */"./src/web/assets/editticket/src/style.scss");


/***/ })

/******/ });