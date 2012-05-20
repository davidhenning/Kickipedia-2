"use strict";

define(['jquery', 'tablesorter'], function($) {
    var exports = {};

    exports.init = function(element) {
        $(element).tablesorter({
            headers: {
                0: {
                    sorter: false
                }
            },
            sortList: [[1,1]]
        });

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