
Header = function(){};
Header.prototype = {
    eventListeners: function () {
    },

    test: function () {
        alert('test');
    },

    init: function () {
        alert('init');

        this.eventListeners();
    }
};