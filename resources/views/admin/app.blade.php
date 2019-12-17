<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @if(isset($dataVersion))
    <meta name="masterdata-version" content="{{ $dataVersion }}">
    @endif

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Styles -->
    <link href="{{ mix('css/admin.css') }}" rel="stylesheet">
</head>
<body class="skin-blue sidebar-mini">
<div id="app"></div>

<!-- Scripts -->
<script src="{{ mix('js/admin/app.js') }}"></script>
</body>
</html>
