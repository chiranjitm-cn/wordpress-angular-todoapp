'use strict';

/* Controllers */

var todosApp = angular.module('todoApp', []);

todosApp.controller('TodoListController', function ( $scope, $http ) {
  $scope.getTodos = function () {
    $http.get("http://localhost/woocomm/wp-json/rest-api-demo/v1/posts/todos")
        .success(function(response) {$scope.todos = response;});
  };
  $scope.getTodos();
});
