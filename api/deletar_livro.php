<?php
// api/deletar_livro.php

require_once __DIR__ . '/../backend/db/Conexao.php';

header('Content-Type: application/json');

// 1. Apenas o método POST será aceito
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // 2. Receber o ID do livro a ser deletado
    //    O ID virá no corpo da requisição POST
    $id = $_POST['id'] ?? null;

    // 3. Validação do ID
    //    Verifica se o ID foi enviado e se é um número inteiro
    if (!$id || !filter_var($id, FILTER_VALIDATE_INT)) {
        echo json_encode(['sucesso' => false, 'mensagem' => 'ID do livro é inválido ou não foi fornecido.']);
        exit;
    }

    try {
        // 4. Preparar e executar a deleção no banco de dados
        $pdo = Conexao::getConexao();
        
        // Query SQL para deletar um livro específico pelo seu ID
        $sql = "DELETE FROM livros WHERE id = :id LIMIT 1";
        
        // Novamente, usamos "Prepared Statements" para segurança máxima
        $stmt = $pdo->prepare($sql);
        
        // Vincula o ID recebido ao parâmetro :id da query
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        
        $stmt->execute();

        // 5. Verificar se alguma linha foi realmente afetada (deletada)
        if ($stmt->rowCount() > 0) {
            // Se o rowCount for maior que 0, o livro foi encontrado e deletado
            echo json_encode(['sucesso' => true, 'mensagem' => 'Livro deletado com sucesso!']);
        } else {
            // Se for 0, nenhum livro com aquele ID foi encontrado no banco
            echo json_encode(['sucesso' => false, 'mensagem' => 'Nenhum livro encontrado com o ID fornecido.']);
        }

    } catch (PDOException $e) {
        // Em caso de erro na comunicação com o banco
        echo json_encode(['sucesso' => false, 'mensagem' => 'Erro ao deletar o livro.']);
    }

} else {
    // Se tentarem acessar o script com outro método (ex: GET)
    echo json_encode(['sucesso' => false, 'mensagem' => 'Método de requisição inválido.']);
}