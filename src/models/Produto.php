<?php
namespace App\Models;

defined('BASE_PATH') or define('BASE_PATH', realpath(dirname(__FILE__) . '/../..'));
require_once BASE_PATH . '/src/config/database.php';

class Produto {
    private static $db;

    // Inicializa a conexão com o banco de dados
    public static function init() {
        global $db;
        self::$db = $db;
    }

    /**
     * Lista produtos em destaque (sugestões)
     * @return array
     */
    public static function listarSugestoes() {
        self::init(); // Garante que a conexão está inicializada
        
        try {
            $stmt = self::$db->prepare("SELECT * FROM produtos WHERE sugestao = 1 ORDER BY RAND() LIMIT 8");
            $stmt->execute();
            return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        } catch (\Exception $e) {
            error_log("Erro ao listar sugestões: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Lista todos os produtos com paginação
     * @param int $pagina
     * @param int $porPagina
     * @return array
     */
    public static function listarTodos($pagina = 1, $porPagina = 12) {
        self::init();
        
        try {
            $offset = ($pagina - 1) * $porPagina;
            $stmt = self::$db->prepare("SELECT * FROM produtos ORDER BY nome LIMIT ? OFFSET ?");
            $stmt->bind_param("ii", $porPagina, $offset);
            $stmt->execute();
            return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        } catch (\Exception $e) {
            error_log("Erro ao listar produtos: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Busca produto por ID
     * @param int $id
     * @return array|null
     */
    public static function buscarPorId($id) {
        self::init();
        
        try {
            $stmt = self::$db->prepare("SELECT * FROM produtos WHERE id = ?");
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $result = $stmt->get_result();
            return $result->num_rows > 0 ? $result->fetch_assoc() : null;
        } catch (\Exception $e) {
            error_log("Erro ao buscar produto: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Lista categorias disponíveis
     * @return array
     */
    public static function listarCategorias() {
        self::init();
        
        try {
            $stmt = self::$db->query("SELECT DISTINCT categoria FROM produtos");
            return $stmt->fetch_all(MYSQLI_COLUMN);
        } catch (\Exception $e) {
            error_log("Erro ao listar categorias: " . $e->getMessage());
            return ['bolos', 'doces', 'kits'];
        }
    }

    /**
     * Adiciona um novo produto
     * @param array $dados
     * @return array
     */
    public static function adicionar($dados) {
        self::init();
        
        try {
            $stmt = self::$db->prepare("INSERT INTO produtos (nome, descricao, preco, imagem, categoria, sugestao) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->bind_param(
                "ssdssi", 
                $dados['nome'],
                $dados['descricao'],
                $dados['preco'],
                $dados['imagem'],
                $dados['categoria'],
                $dados['sugestao']
            );
            $stmt->execute();
            return ['success' => true, 'id' => $stmt->insert_id];
        } catch (\Exception $e) {
            error_log("Erro ao adicionar produto: " . $e->getMessage());
            return ['success' => false, 'message' => 'Erro ao adicionar produto'];
        }
    }

    /**
     * Atualiza um produto existente
     * @param int $id
     * @param array $dados
     * @return bool
     */
    public static function atualizar($id, $dados) {
        self::init();
        
        try {
            $stmt = self::$db->prepare("UPDATE produtos SET nome = ?, descricao = ?, preco = ?, imagem = ?, categoria = ?, sugestao = ? WHERE id = ?");
            $stmt->bind_param(
                "ssdssii",
                $dados['nome'],
                $dados['descricao'],
                $dados['preco'],
                $dados['imagem'],
                $dados['categoria'],
                $dados['sugestao'],
                $id
            );
            return $stmt->execute();
        } catch (\Exception $e) {
            error_log("Erro ao atualizar produto: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Remove um produto
     * @param int $id
     * @return bool
     */
    public static function remover($id) {
        self::init();
        
        try {
            $stmt = self::$db->prepare("DELETE FROM produtos WHERE id = ?");
            $stmt->bind_param("i", $id);
            return $stmt->execute();
        } catch (\Exception $e) {
            error_log("Erro ao remover produto: " . $e->getMessage());
            return false;
        }
    }
}

// Inicializa a conexão quando a classe for carregada
Produto::init();