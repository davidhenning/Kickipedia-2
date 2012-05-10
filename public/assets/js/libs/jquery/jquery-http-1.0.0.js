(function() {
    jQuery.extend({
        put: function(url, callback) {
            return jQuery.ajax(url, {type: 'PUT', success: callback});
        },
        
        delete: function(url, callback) {
            return jQuery.ajax(url, {type: 'DELETE', success: callback});
        }
    });     
})();