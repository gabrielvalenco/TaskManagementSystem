<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Categoria</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/custom-styles.css') }}">
    <link rel="stylesheet" href="{{ asset('css/category-style.css') }}">
</head>
<body>
    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>Editar Categoria</h1>
            <a href="{{ route('categories.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i>Voltar
            </a>
        </div>
        <form action="{{ route('categories.update', $category->id) }}" method="POST" class="category-form">
            @csrf
            @method('PUT')
            <div class="form-group">
                <label for="name">Nome da Categoria</label>
                <input type="text" name="name" class="form-control" id="name" value="{{ $category->name }}" required>
            </div>
            <div class="form-group">
                <label for="description">Descrição</label>
                <textarea name="description" class="form-control" id="description" rows="3">{{ $category->description }}</textarea>
            </div>
            <div class="form-group">
                <button type="submit" class="btn btn-primary">Atualizar Categoria</button>
            </div>
        </form>
    </div>
</body>
</html>
