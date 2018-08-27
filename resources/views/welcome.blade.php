<!doctype html>
<html lang="{{ app()->getLocale() }}">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
		<meta name="csrf-token" content="{{ csrf_token() }}">

        <title>Programming Challenge - Jesse Alonzo</title>

        <link href="{{ URL::asset('public/css/app.css') }}" rel="stylesheet" type="text/css">
		<script type="text/javascript" src="{{ URL::asset('public/js/app.js') }}"></script>
		<script type="text/javascript" src="{{ URL::asset('public/js/fileProcessing.js') }}"></script>
		<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.2.0/css/all.css" integrity="sha384-hWVjflwFxL6sNzntih27bfxkr27PmbbK/iSvJ+a4+0owXq79v+lsFkW54bOGbiDQ" crossorigin="anonymous">
    </head>
    <body>
		<nav class="navbar navbar-dark bg-dark">
			<span class="navbar-brand mb-0 h1">Programming Challenge - Jesse Alonzo</span>
		</nav>
		<div class="container mt-5 p-2">
			<div class="card p-3">
				<form enctype="multipart/form-data">
					{{ csrf_field() }}
					<div class="card-body">
						<h5>Select the Invitation CSV File</h5>
						<div class="input-group mb-3">
							<input type="file" class="form-control" name="inviteFile" accept=".csv">
						</div>
						<div class="float-right">
							<a href="public/files/resources.zip" class="btn btn-light">Download Documentation</a>
							<button type="submit" class="btn btn-primary " disabled>Submit</button>
						</div>
					</div>
				</form>
			</div>
		</div>
    </body>
</html>
