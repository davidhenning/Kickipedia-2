define(["jquery"], function ($){
    var _initModuleLoader = function (context) {
        $('[data-module]', context).each(function(index) {
            var module = $(this).data('module');
            var parameters = $(this).data('module-parameters') || '';
            parameters = parameters.split(',');
            parameters.unshift(this);
            
            require([module], function (module) {
              module.init.apply(module, parameters);
            });
        });
    };

    return {
        load: function(context) {
            _initModuleLoader(context);
        }
    };
});