'use strict';

/* Controllers */

var todosApp = angular.module('todoApp', []);

todosApp.controller('TodoListController', function ( $scope, $http ) {
  $scope.getTodos = function () {
    //$http.get("http://localhost/woocomm/wp-json/rest-api-demo/v1/posts/todos")
    $http.get("http://localhost/woocomm/wp-json/wp/v2/todo")
        .success(function(response) {
        	$scope.todos = response;
        	//alert(JSON.stringify($scope.todos));
        });
  };

  $scope.addTodo = function() {
    $http.post("http://localhost/woocomm/wp-json/rest-api-demo/v1/posts/todos/add", $scope.addtodoitem )
    .success( function ( data ) {
        $scope.getTodos();
        $scope.addtodoitem = '';
      });
    return;
  };

  $scope.getTodos();
});
