(function() {
    'use strict';

    var requireConfig = {
            paths: {
                'jquery': 'vendor/jquery/jquery-1.7.2',
                'moduleloader': 'core/moduleloader',
                'jqhttp': 'vendor/jquery-plugins/jquery-http-1.0.0',
                'tablesorter': 'vendor/jquery-plugins/jquery.tablesorter'
            }
        };

    require.config(requireConfig);

    require(['jquery', 'moduleloader'], function(jQuery, moduleloader){
        jQuery.noConflict();
        moduleloader.load();
    });
})();