(function() {
    'use strict';

    var requireConfig = {
            paths: {
                'moduleloader': 'core/moduleloader',
                'jquery': 'vendor/jquery/1.7.2',             
                'jqhttp': 'vendor/jquery-plugins/http-1.0.0',
                'tablesorter': 'vendor/jquery-plugins/tablesorter-2.0.5'
            }
        };

    require.config(requireConfig);

    require(['jquery', 'moduleloader'], function(jQuery, moduleloader){
        jQuery.noConflict();
        moduleloader.load();
    });
})();