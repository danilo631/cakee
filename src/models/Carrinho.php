<?php
require_once('../src/config/database.php');
require_once('../src/models/Produto.php');

class Carrinho {
    public static function adicionarItem($usuarioId, $produtoId, $quantidade = 1) {
        global $pdo;
        
        try {
            // Verifica se o item já está no carrinho
            $stmt = $pdo->prepare("SELECT id, quantidade FROM carrinho 
                                  WHERE usuario_id = :usuario_id AND produto_id = :produto_id");
            $stmt->bindValue(':usuario_id', $usuarioId);
            $stmt->bindValue(':produto_id', $produtoId);
            $stmt->execute();
            
            $item = $stmt->fetch();
            
            if ($item) {
                // Atualiza quantidade
                $novaQuantidade = $item['quantidade'] + $quantidade;
                $stmt = $pdo->prepare("UPDATE carrinho SET quantidade = :quantidade 
                                      WHERE id = :id");
                $stmt->bindValue(':quantidade', $novaQuantidade);
                $stmt->bindValue(':id', $item['id']);
            } else {
                // Adiciona novo item
                $stmt = $pdo->prepare("INSERT INTO carrinho (usuario_id, produto_id, quantidade) 
                                      VALUES (:usuario_id, :produto_id, :quantidade)");
                $stmt->bindValue(':usuario_id', $usuarioId);
                $stmt->bindValue(':produto_id', $produtoId);
                $stmt->bindValue(':quantidade', $quantidade);
            }
            
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Erro ao adicionar item ao carrinho: " . $e->getMessage());
            return false;
        }
    }

    public static function listarItens($usuarioId) {
        global $pdo;
        
        try {
            $stmt = $pdo->prepare("SELECT p.id, p.nome, p.descricao, p.preco, p.imagem, 
                                  c.quantidade, (p.preco * c.quantidade) as subtotal
                                  FROM carrinho c
                                  JOIN produtos p ON c.produto_id = p.id
                                  WHERE c.usuario_id = :usuario_id");
            $stmt->bindValue(':usuario_id', $usuarioId);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::PARAM_ASSOC);
        } catch (PDOException $e) {
            error_log("Erro ao listar itens do carrinho: " . $e->getMessage());
            return [];
        }
    }

    public static function removerItem($usuarioId, $produtoId) {
        global $pdo;
        
        try {
            $stmt = $pdo->prepare("DELETE FROM carrinho 
                                  WHERE usuario_id = :usuario_id AND produto_id = :produto_id");
            $stmt->bindValue(':usuario_id', $usuarioId);
            $stmt->bindValue(':produto_id', $produtoId);
            
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Erro ao remover item do carrinho: " . $e->getMessage());
            return false;
        }
    }

    public static function limparCarrinho($usuarioId) {
        global $pdo;
        
        try {
            $stmt = $pdo->prepare("DELETE FROM carrinho WHERE usuario_id = :usuario_id");
            $stmt->bindValue(':usuario_id', $usuarioId);
            
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Erro ao limpar carrinho: " . $e->getMessage());
            return false;
        }
    }
}