<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.3/css/bootstrap.min.css'
        integrity='sha512-jnSuA4Ss2PkkikSOLtYs8BlYIeeIK1h99ty4YfvRPAlzr377vr3CXDb7sb7eEEBYjDtcYj+AjBH3FLv5uSJuXg=='
        crossorigin='anonymous' />
    <title>Document</title>
    <div class="container mt-5">
        <h2 class="mb-4">Departments</h2>
        <ul class="list-group">
            @foreach ($departments as $index => $department)
                <li class="list-group-item d-flex justify-content-between align-items-start">
                    <div class="ms-2 me-auto">
                        <div class="fw-bold">{{ $index + 1 }}. {{ $department->name }}</div>
                        <span class="text-muted">{{ $department->description }}</span>
                    </div>
                    <span class="badge bg-primary rounded-pill">{{ $index + 1 }}</span>
                </li>
            @endforeach
        </ul>
    </div>
</head>

<body>

</body>

</html>
