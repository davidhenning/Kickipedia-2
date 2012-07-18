"use strict";

define(['jquery', 'tablesorter', 'jqhttp'], function($) {
    var _loadTableSorter = function(element) {
        $(element).tablesorter({
            headers: {
                0: {
                    sorter: false
                }
            },
            sortList: [[1,1]]
        });
    }

    var _bindDeleteEvent = function(element) {
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

    return {
        init: function(element) {
            _loadTableSorter(element);
            _bindDeleteEvent(element);
        }
    };
});