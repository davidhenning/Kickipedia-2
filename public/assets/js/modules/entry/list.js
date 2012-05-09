"use strict";

define(['jquery'], function($) {
    var exports = {};

    exports.init = function(element) {
        $('.tools a.delete', element).click(function(e) {
            e.preventDefault();
            alert('Delete?');
        });
    }

    return exports;
});