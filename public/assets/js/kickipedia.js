(function() {
    'use strict';

    var requireConfig = {
            paths: {
                'jquery': 'libs/jquery/jquery-1.7.2',
                'moduleloader': 'libs/core/moduleloader'
            }
        };

    require.config(requireConfig);

    require(['jquery', 'moduleloader'], function(jQuery, moduleloader){
        jQuery.noConflict();
        moduleloader.load();
    });
})();