<!DOCTYPE html>
<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{(string)$title}}</title>
    <!-- Bootstrap 5 CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.0.2/css/bootstrap.min.css" integrity="sha512-riN7ojNJJz4Up7jl4+4tnZJwVcMQi0czx7RRSMD1gO11Dvz3Pqdx76g6l5Sw5glZw0Qh1JxgmyVU5fd0o0NV6w==" crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>
<body>
    <div class="container">
        <div class="row">
            <div class="col-md-6 mx-auto mt-3">
                <div class="card">
                    <div class="card-header">
                        <h1 class="text-center">{{(string)$title}}</h1>
                    </div>
                    <div class="card-body">
                        <p class="lead">{{$message}}</p>
                        <p><strong>Sent to:</strong> {{(string)$email}}</p>
                        {{-- @if($imagePath)
                        <img src="{{ $imagePath }}" alt="image" class="img-fluid">
                        @endif --}}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.0.2/js/bootstrap.min.js" integrity="sha512-S+kmwKtJcgzAaNx8KrGCHCpEo0tbmKqv7bOYlF/Pf7VZ+O47TkdSeagcKb8RtMxySm/B1imGOt3qYvIJ8HuKpA==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
</body>
</html>
