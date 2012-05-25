(function($) {
    $.extend({
        put: function(url, callback) {
            return $.ajax(url, {type: 'PUT', success: callback});
        },
        
        delete: function(url, callback) {
            return $.ajax(url, {type: 'DELETE', success: callback});
        }
    });     
})(jQuery);