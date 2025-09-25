<?php
// backend/models/ColecaoDeLivros.php

require_once __DIR__ . '/../db/Conexao.php';
require_once __DIR__ . '/Livro.php';

class ColecaoDeLivros implements Iterator {
    private $posicao = 0;
    private $livros = [];

    public function __construct() {
        $this->posicao = 0;
        $pdo = Conexao::getConexao();
        $stmt = $pdo->query("SELECT * FROM livros ORDER BY titulo ASC");
        
        // Mapeia o resultado do banco para a nossa classe Livro
        $this->livros = $stmt->fetchAll(PDO::FETCH_CLASS, 'Livro');
    }

    // Métodos da Interface Iterator

    public function rewind(): void {
        $this->posicao = 0;
    }

    public function current(): mixed {
        return $this->livros[$this->posicao];
    }

    public function key(): mixed {
        return $this->posicao;
    }

    public function next(): void {
        ++$this->posicao;
    }

    public function valid(): bool {
        return isset($this->livros[$this->posicao]);
    }
}