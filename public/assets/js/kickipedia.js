(function() {
    'use strict';

    var requireConfig = {
            paths: {
                'jquery': 'libs/jquery/jquery-1.7.2',
                'moduleloader': 'libs/core/moduleloader',
                'jqhttp': 'libs/jquery/jquery-http-1.0.0',
                'tablesorter': 'libs/jquery/jquery.tablesorter'
            }
        };

    require.config(requireConfig);

    require(['jquery', 'moduleloader', 'jqhttp'], function(jQuery, moduleloader){
        jQuery.noConflict();
        moduleloader.load();
    });
})();