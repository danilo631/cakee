<?php
namespace App\Models;

defined('BASE_PATH') or define('BASE_PATH', realpath(dirname(__FILE__) . '/../..'));
require_once BASE_PATH . '/src/config/database.php';

class Usuario {
    private $db;

    public function __construct() {
        global $db;
        $this->db = $db;
    }

    /**
     * Cadastra um novo usuário
     * @param array $dados ['nome', 'email', 'senha', 'tipo']
     * @return array ['success', 'message', 'id']
     */
 public function cadastrar($dados) {
    // Validação dos dados
    if (empty($dados['nome'])) {
        return array('success' => false, 'message' => 'Nome é obrigatório');
    }

    if (!filter_var($dados['email'], FILTER_VALIDATE_EMAIL)) {
        return array('success' => false, 'message' => 'Email inválido');
    }

    if (strlen($dados['senha']) < 6) {
        return array('success' => false, 'message' => 'Senha deve ter pelo menos 6 caracteres');
    }

    $dados['tipo'] = $dados['tipo'] ?? 'cliente';

    try {
        // Verifica se email já existe
        $stmt = $this->db->prepare("SELECT id FROM usuarios WHERE email = :email");
        $stmt->bindValue(':email', $dados['email']);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            return array('success' => false, 'message' => 'Email já cadastrado');
        }

        // Hash da senha
        $senhaHash = password_hash($dados['senha'], PASSWORD_DEFAULT);

        // Insere no banco
        $stmt = $this->db->prepare("INSERT INTO usuarios (nome, email, senha, tipo) VALUES (:nome, :email, :senha, :tipo)");
        $stmt->bindValue(':nome', $dados['nome']);
        $stmt->bindValue(':email', $dados['email']);
        $stmt->bindValue(':senha', $senhaHash);
        $stmt->bindValue(':tipo', $dados['tipo']);
        $stmt->execute();

        return array(
            'success' => true,
            'message' => 'Usuário cadastrado com sucesso',
            'id' => $this->db->lastInsertId()
        );

    } catch (Exception $e) {
        error_log("Erro ao cadastrar usuário: " . $e->getMessage());
        return array('success' => false, 'message' => 'Erro ao cadastrar usuário');
    }
}

    /**
     * Autentica um usuário
     * @param string $email
     * @param string $senha
     * @return array ['success', 'message', 'usuario']
     */
    public function autenticar($email, $senha) {
        try {
            $stmt = $this->db->prepare("SELECT * FROM usuarios WHERE email = ?");
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows === 0) {
                return array('success' => false, 'message' => 'Email não encontrado');
            }

            $usuario = $result->fetch_assoc();

            if (!password_verify($senha, $usuario['senha'])) {
                return array('success' => false, 'message' => 'Senha incorreta');
            }

            // Remove a senha do array antes de retornar
            unset($usuario['senha']);

            return array(
                'success' => true,
                'message' => 'Login realizado com sucesso',
                'usuario' => $usuario
            );

        } catch (Exception $e) {
            error_log("Erro ao autenticar usuário: " . $e->getMessage());
            return array('success' => false, 'message' => 'Erro ao realizar login');
        }
    }

    /**
     * Busca usuário por ID
     * @param int $id
     * @return array|null
     */
    public function buscarPorId($id) {
        try {
            $stmt = $this->db->prepare("SELECT id, nome, email, tipo, criado_em FROM usuarios WHERE id = ?");
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows === 0) {
                return null;
            }

            return $result->fetch_assoc();

        } catch (Exception $e) {
            error_log("Erro ao buscar usuário: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Atualiza dados do usuário
     * @param int $id
     * @param array $dados
     * @return bool
     */
    public function atualizar($id, $dados) {
        try {
            $campos = array();
            $valores = array();
            $tipos = '';
            
            foreach ($dados as $campo => $valor) {
                if (in_array($campo, array('nome', 'email', 'tipo'))) {
                    $campos[] = "$campo = ?";
                    $valores[] = $valor;
                    $tipos .= 's';
                }
            }
            
            if (empty($campos)) {
                return false;
            }
            
            $valores[] = $id;
            $tipos .= 'i';
            
            $sql = "UPDATE usuarios SET " . implode(', ', $campos) . " WHERE id = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->bind_param($tipos, ...$valores);
            
            return $stmt->execute();

        } catch (Exception $e) {
            error_log("Erro ao atualizar usuário: " . $e->getMessage());
            return false;
        }
    }
}