"use strict";

define(['jquery'], function($) {
    var exports = {};

    exports.init = function(element) {
        $('.tools a.delete', element).click(function(e) {
            e.preventDefault();
            var row = $(this).parents('tr');

            if(window.confirm('Eintrag wirklich löschen?') === true) {
                $.delete($(this).data('rest-action'), function(data, status) {
                    if(status == 'success') {
                        row.fadeOut(400, function() {
                            $(this).remove();
                        });
                    }
                });
            }          
        });
    }

    return exports;
});