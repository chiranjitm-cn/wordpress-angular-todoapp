<?php
/**
 * Template Name: Todo App Page
 *
 */?>
<?php get_header();?>
<style type="text/css">
	.done-true {
		text-decoration: line-through;
		color: grey;
	}
	.cursor-pointer {
		cursor: pointer;
		text-decoration: none;
	}
	</style>

		<div class="container" ng-app="todoApp">
			<div class="jumbotron">
				<h3>Todo List</h3>
				<div ng-controller="TodoListController">
					<div class="btn-group">
						<div class="btn">
							<span class="cursor-pointer" ng-click="getTodos()">
								<a>Show All</a>
							</span>
						</div>
						<div class="btn">
							<span class="cursor-pointer" ng-click="hideCompleted()">
								<a>Hide Completed</a>
							</span>
						</div>
						<div class="btn">
							<span class="cursor-pointer" ng-click="showCompleted()">
								<a>Show Completed</a>
							</span>
						</div>
					</div>

					<p><input type="text" ng-model="search" placeholder="Search"  class="search-query"></p>
					<ul class="unstyled" ui-sortable ng-model="todos">
						<li ng-repeat="todo in todos | filter : search ">
							<label class="checkbox">
								<input type="checkbox" ng-checked="todo.is_done =='true'" ng-model="todo.is_done" ng-click='updateTodos(todo)'>
								<span class="done-{{todo.is_done}}">{{todo.title.rendered}}</span>
								<span ng-click='deleteTodos(todo)' class="glyphicon glyphicon-remove cursor-pointer"></span>
							</label>

						</li>
					</ul>
					<form ng-submit="addTodo()">
						<input type="text" ng-model="addtodoitem"  size="30"
						placeholder="add new todo here">
						<input class="btn-primary" type="submit" value="add">
					</form>
				</div>
			</div>
		</div>
<?php get_footer();?>