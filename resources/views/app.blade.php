<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0" />
    @vite('resources/js/app.js')
    @inertiaHead

    <link rel="stylesheet" href="{{ asset('vendors/bootstrap-5.0.2-dist/css/bootstrap.min.css') }}">
  </head>
  <body>
    @inertia


    <script src="{{ asset('vendors/bootstrap-5.0.2-dist/js/bootstrap.min.js') }}"></script>
  </body>
</html>
