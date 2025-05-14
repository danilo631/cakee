<?php
require_once('../models/Produto.php');

class ProdutoController {
    public static function listar() {
        return Produto::listarTodos();
    }

    public static function listarPorCategoria($categoria) {
        return Produto::listarPorCategoria($categoria);
    }

    public static function buscarPorId($id) {
        return Produto::buscarPorId($id);
    }

    public static function salvar($dados, $imagem) {
        $nome = $dados['nome'];
        $descricao = $dados['descricao'];
        $preco = $dados['preco'];
        $categoria = $dados['categoria'];
        $sugestao = isset($dados['sugestao']) ? 1 : 0;
        
        // Processar upload da imagem
        $nomeImagem = self::processarUploadImagem($imagem);
        
        if (!$nomeImagem) {
            return ['success' => false, 'message' => 'Erro no upload da imagem'];
        }
        
        $produto = new Produto($nome, $descricao, $preco, $nomeImagem, $categoria, $sugestao);
        
        if (Produto::salvar($produto)) {
            return ['success' => true];
        } else {
            return ['success' => false, 'message' => 'Erro ao salvar produto'];
        }
    }

    private static function processarUploadImagem($imagem) {
        $diretorio = '../../assets/images/produtos/';
        $nomeArquivo = uniqid() . '_' . basename($imagem['name']);
        $caminhoCompleto = $diretorio . $nomeArquivo;
        
        if (move_uploaded_file($imagem['tmp_name'], $caminhoCompleto)) {
            return 'assets/images/produtos/' . $nomeArquivo;
        }
        
        return false;
    }
}