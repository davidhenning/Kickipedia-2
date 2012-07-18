"use strict";

define(['jquery'], function($) {
    var _bindSearchEvent = function(element) {
        $('a', element).click(function(e) {
            e.preventDefault();
            element.submit();
        });
    }

    return {
    	init: function(element) {
    		_bindSearchEvent(element);
    	}
    };
});