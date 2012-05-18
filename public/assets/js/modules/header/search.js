"use strict";

define(['jquery'], function($) {
    var exports = {};

    exports.init = function(element) {
        $('a', element).click(function(e) {
            e.preventDefault();
            element.submit();
        })
    }

    return exports;
});