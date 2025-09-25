<?php
// api/get_livros.php

header('Content-Type: application/json');
require_once __DIR__ . '/../backend/models/ColecaoDeLivros.php';

$colecao = new ColecaoDeLivros();
$resultado = [];

// A mágica do Iterator! Podemos usar foreach diretamente no objeto.
foreach ($colecao as $livro) {
    $resultado[] = [
        'id' => $livro->id,
        'titulo' => $livro->titulo,
        'autor' => $livro->autor,
        'ano' => $livro->ano_publicacao,
        'genero' => $livro->genero
    ];
}

echo json_encode($resultado);