define(["jquery"], function ($){
    var exports = {};

    exports.load = function (context) {
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

  return exports;
});