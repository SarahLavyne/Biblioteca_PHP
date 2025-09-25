<?php
// api/cadastrar_livro.php

// Inclui o arquivo de conexão
require_once __DIR__ . '/../backend/db/Conexao.php';

// Define o cabeçalho da resposta como JSON
header('Content-Type: application/json');

// --- Início da lógica de cadastro ---

// 1. Verificar se o método da requisição é POST
//    Isso garante que o script só execute quando o formulário for enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // 2. Coletar e limpar os dados do formulário
    //    Usamos trim() para remover espaços em branco no início e no fim
    $titulo = trim($_POST['titulo'] ?? '');
    $autor = trim($_POST['autor'] ?? '');
    $ano = $_POST['ano_publicacao'] ?? null;
    $genero = trim($_POST['genero'] ?? '');

    // 3. Validação básica
    //    Verifica se os campos obrigatórios não estão vazios
    if (empty($titulo) || empty($autor)) {
        // Se a validação falhar, retorna um erro em JSON
        echo json_encode(['sucesso' => false, 'mensagem' => 'Erro: Título e Autor são obrigatórios.']);
        exit; // Para a execução do script
    }

    try {
        // 4. Preparar a inserção no banco de dados
        $pdo = Conexao::getConexao();
        
        $sql = "INSERT INTO livros (titulo, autor, ano_publicacao, genero) VALUES (:titulo, :autor, :ano, :genero)";
        
        // Usamos "Prepared Statements" (declarações preparadas) para evitar SQL Injection.
        // É uma prática de segurança essencial!
        $stmt = $pdo->prepare($sql);
        
        // 5. Vincular os valores aos parâmetros da query
        $stmt->bindValue(':titulo', $titulo);
        $stmt->bindValue(':autor', $autor);
        
        // Para valores que podem ser nulos, como ano e gênero, tratamos diferente
        $stmt->bindValue(':ano', !empty($ano) ? $ano : null, PDO::PARAM_INT);
        $stmt->bindValue(':genero', !empty($genero) ? $genero : null);
        
        // 6. Executar a query
        $stmt->execute();

        // 7. Retornar uma resposta de sucesso em JSON
        echo json_encode(['sucesso' => true, 'mensagem' => 'Livro cadastrado com sucesso!']);

    } catch (PDOException $e) {
        // Em caso de erro no banco de dados, retorna uma mensagem de erro genérica
        echo json_encode(['sucesso' => false, 'mensagem' => 'Erro ao cadastrar livro no banco de dados.']);
    }

} else {
    // Se o método não for POST, retorna um erro
    echo json_encode(['sucesso' => false, 'mensagem' => 'Método de requisição inválido.']);
}