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
/******/ 	return __webpack_require__(__webpack_require__.s = 2);
/******/ })
/************************************************************************/
/******/ ({

/***/ "./src/web/assets/availableticketfield/src/EventTicketTypeMappingRemover.js":
/*!**********************************************************************************!*\
  !*** ./src/web/assets/availableticketfield/src/EventTicketTypeMappingRemover.js ***!
  \**********************************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

(function ($) {
  /** global: Craft */

  /** global: Garnish */
  Craft.EventTicketTypeMappingRemover = Garnish.Base.extend({
    $deleteLinks: null,
    $selectButton: null,
    // $spinner: null,
    init: function init() {
      var _this = this;

      this.$deleteLinks = document.querySelectorAll('.deleteMappingLink');
      this.$selectButton = document.querySelector('#availableTickets-field .buttons .menubtn'); // this.$spinner = $('<div class="spinner hidden" style="margin-left: 24px;" />').appendTo(this.$typeSelect);

      this.$deleteLinks.forEach(function (link) {
        _this.initDeleteButton(link);
      });
    },
    initDeleteButton: function initDeleteButton(button) {
      var _this2 = this;

      this.addListener(button, 'click', function (evt) {
        _this2.deleteTicketTypeMapping(evt);
      });
    },
    deleteTicketTypeMapping: function deleteTicketTypeMapping(evt) {
      var deleteBtn = evt.currentTarget;
      var ticketTypeHandle = deleteBtn.dataset['handle'];
      this.deleteAction(evt, ticketTypeHandle);
    },
    deleteAction: function deleteAction(evt, ticketTypeHandle) {
      this.removeMappingBlock(ticketTypeHandle);
      this.showBlockTypeInMenu(ticketTypeHandle);
      this.showAddTicketTypeButton();
    },
    removeMappingBlock: function removeMappingBlock(typeHandle) {
      $("#availableTickets-field .blocks #".concat(typeHandle)).remove();
    },
    showBlockTypeInMenu: function showBlockTypeInMenu(ticketTypeHandle) {
      var blockTypeLink = document.querySelector(".js-ticketTypeList li.".concat(ticketTypeHandle));

      if (blockTypeLink) {
        blockTypeLink.classList.remove('hidden');
      }
    },
    showAddTicketTypeButton: function showAddTicketTypeButton() {
      $(this.$selectButton).removeClass('hidden');
    }
  });
})(jQuery);

/***/ }),

/***/ "./src/web/assets/availableticketfield/src/TicketTypeSelector.js":
/*!***********************************************************************!*\
  !*** ./src/web/assets/availableticketfield/src/TicketTypeSelector.js ***!
  \***********************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

(function ($) {
  /** global: Craft */

  /** global: Garnish */
  Craft.TicketTypeSelector = Garnish.Base.extend({
    $typeSelect: null,
    $typeSelectLinks: null,
    $ticketTypeList: null,
    $blockContainer: null,
    $selectButton: null,
    $spinner: null,
    init: function init() {
      var _this = this;

      this.$typeSelect = document.querySelector('#availableTickets-field .buttons');
      this.$typeSelectLinks = document.querySelectorAll('.js-ticketTypeLink');
      this.$ticketTypeList = document.querySelector('.js-ticketTypeList');
      this.$blockContainer = document.querySelector('#availableTickets-field .blocks');
      this.$selectButton = document.querySelector('#availableTickets-field .buttons .menubtn');
      this.$spinner = $('<div class="spinner hidden" style="margin-left: 24px;" />').appendTo(this.$typeSelect);
      this.$typeSelectLinks.forEach(function (link) {
        _this.addListener(link, 'click', function (evt) {
          var ticketTypeHandle = evt.currentTarget.dataset['type'];

          _this.onTypeChange(evt, ticketTypeHandle);
        });
      });
    },
    onTypeChange: function onTypeChange(evt, ticketTypeHandle) {
      this.$spinner.removeClass('hidden');
      Craft.postActionRequest('eventsky/events/add-new-ticket-type', {
        'ticketType': ticketTypeHandle
      }, $.proxy(function (response, textStatus) {
        this.$spinner.addClass('hidden');

        if (textStatus === 'success') {
          this.addMappingBlock(response);
          this.hideBlockTypeFromMenu(evt);

          if (this.allTicketTypesMapped()) {
            this.hideAddTicketTypeButton();
          }
        }
      }, this));
    },
    addMappingBlock: function addMappingBlock(response) {
      var html = response.fieldHtml;
      var node = $(html)[0];
      var button = node.querySelector('.deleteMappingLink');
      window.eventTicketTypeMappingRemover.initDeleteButton(button);
      this.$blockContainer.append(node);
      Craft.initUiElements($(this.$blockContainer));
      Craft.appendBodyHtml(response.bodyHtml);
    },
    hideBlockTypeFromMenu: function hideBlockTypeFromMenu(evt) {
      $(evt.currentTarget).parent().addClass('hidden');
    },
    hideAddTicketTypeButton: function hideAddTicketTypeButton() {
      $(this.$selectButton).addClass('hidden');
    },
    allTicketTypesMapped: function allTicketTypesMapped() {
      return this.$ticketTypeList.querySelectorAll('li:not(.hidden)').length === 0;
    }
  });
})(jQuery);

/***/ }),

/***/ "./src/web/assets/availableticketfield/src/index.js":
/*!**********************************************************!*\
  !*** ./src/web/assets/availableticketfield/src/index.js ***!
  \**********************************************************/
/*! no exports provided */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _TicketTypeSelector__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./TicketTypeSelector */ "./src/web/assets/availableticketfield/src/TicketTypeSelector.js");
/* harmony import */ var _TicketTypeSelector__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_TicketTypeSelector__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _EventTicketTypeMappingRemover__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./EventTicketTypeMappingRemover */ "./src/web/assets/availableticketfield/src/EventTicketTypeMappingRemover.js");
/* harmony import */ var _EventTicketTypeMappingRemover__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_EventTicketTypeMappingRemover__WEBPACK_IMPORTED_MODULE_1__);



/***/ }),

/***/ 2:
/*!****************************************************************!*\
  !*** multi ./src/web/assets/availableticketfield/src/index.js ***!
  \****************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

module.exports = __webpack_require__(/*! /Users/sarah/Projects/CraftPlugins/EventPlugin/FREDMANSKY_eventplugin/src/web/assets/availableticketfield/src/index.js */"./src/web/assets/availableticketfield/src/index.js");


/***/ })

/******/ });