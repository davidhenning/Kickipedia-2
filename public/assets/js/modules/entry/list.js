"use strict";

define(['jquery'], function($) {
    var exports = {};

    exports.init = function(element) {
        $('.tools a.delete', element).click(function(e) {
            if(window.confirm('Eintrag wirklich löschen?') === false) {
                e.preventDefault();
            }

            // delete with ajax
        });
    }

    return exports;
});