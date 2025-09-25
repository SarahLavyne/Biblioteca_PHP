<?php
// backend/models/Livro.php

class Livro {
    public int $id;
    public string $titulo;
    public string $autor;
    public int $ano_publicacao;
    public string $genero;
    public bool $disponivel;
}