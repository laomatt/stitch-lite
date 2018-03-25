<?
use App\Models\User;
?>
@extends('layout')

@section('content')

<div class="container">
	
	<h1>Create a New User</h1>
	<form action="#">	
		<? 
		echo Form::token(); 
		?>
		<div class="form-group">
			<label for="name">Name</label>
			<input type="text" name='name' class="form-control" placeholder="name">
		</div>

		<div class="form-group">
			<label for="email">Email address</label>
			<input type="text" name='email' class="form-control" placeholder="email">
		</div>

		<div class="form-group">
			<label for="email">Password</label>
			<input type="password" name='password' class="form-control" placeholder="password">
		</div>


		</div>

	</form>
</div>
@stop
