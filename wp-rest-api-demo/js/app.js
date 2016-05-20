'use strict';

/* Controllers */

var todosApp = angular.module('todoApp', []);
var siteurl = wnm_custom.site_url;

todosApp.controller('TodoListController', function ( $scope, $http ) {

    $scope.getTodos = function () {
        $http.get( siteurl + "/wp-json/wp/v2/todo" ).success( function( response ) {
            $scope.todos = response;
        });
    };

    $scope.addTodo = function() {
        var itemsObject = [];
        var arr_items_dates = '';
        var items = $scope.addtodoitem;
        var items_todos = items.todo;
        var arr_items_todos = items_todos.split(',');

        if( items.duedate ) {
            var items_dates = items.duedate;
            arr_items_dates = items_dates.split(',');
        } else {
            arr_items_dates = '';
        }

        for (var i = 0; i < arr_items_todos.length; i++ ) {
            var item_todo = arr_items_todos[i];
            var item_date = arr_items_dates[i];
            var itemObj = {
                itemname: item_todo,
                duedate: item_date
            };
            itemsObject.push(itemObj);
        }
        var req = {
            method: 'POST',
            url: siteurl + '/wp-json/wp/v2/posts/todos/add',
            data: { 'todos' : itemsObject }
        }
        $http(req).success( function ( data ) {
            $scope.getTodos();
            $scope.addtodoitem = '';
        })
        return;
    };

    $scope.updateTodos = function(todo) {
        if ( 'true' === todo.is_done ) {
            var is_done_val = "false";
        } else {
            var is_done_val = "true";
        };
        var req = {
            method: 'PUT',
            url: siteurl + '/wp-json/wp/v2/posts/todos/update',
            data: { 'todo_id' : todo.id, 'todo_is_done' : is_done_val}
        }
        $http(req).success( function ( data ) {
            $scope.getTodos();
            $scope.addtodoitem = '';
        })
        return;
    }

    $scope.deleteTodos = function(todo) {
    var req = {
            method: 'DELETE',
            url: siteurl + '/wp-json/wp/v2/posts/todos/delete/'+todo.id,
        }
        $http(req).success( function ( data ) {
            $scope.getTodos();
            $scope.addtodoitem = '';
        })
        return;
    }

    $scope.getTodos();
});
