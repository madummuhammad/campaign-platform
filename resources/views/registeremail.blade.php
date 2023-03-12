<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Document</title>
</head>
<body>
	<h1>Silahkan klik link berikut</h1>
	@if(env('APP_ENV')=='local')
	<a href="http://localhost:4200/register/confirm/{{$token}}">Klik link</a>
	@endif
</body>
</html>