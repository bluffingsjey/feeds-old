/*
*	The Main Module
*	Users Application Module
*/
var app = angular.module('handhApp',['ngRoute','ngAnimate']);

/*
*	cmdate()
*	custom filter for the date formating
*/
app.filter('cmdate', [
    '$filter', function($filter) {
        return function(input, format) {
            return $filter('date')(new Date(input), format);
        };
    }
]);

/*
*	animation
*/
app.animation('.ng-slide-down', function() {
  return {
    enter: function(element, done) {
      element.hide().slideDown()
      //return function(cancelled) {};
    },
    leave: function(element, done) { 
      element.slideUp();
    },
  };
});