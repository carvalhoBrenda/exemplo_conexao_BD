<?php

    function novaConexao()
    {
        $dsn = 'mysql:host=localhost;dbname=cadastro';
        $usuario = 'root';
        $senha = '';
        try
        {
            //cria objeto conexão da classe PDO
            $conexao = new PDO ($dsn, $usuario, $senha);
            //retorna a conexão
            return $conexao;
        }
        catch(PDOException $e)
        {
            echo 'Erro ao conectar com o banco de dados'. $e->getMessage();
        }
    } //fecha a função

    // novaConexao(); //executa a função apenas para testar a conexão
    //echo "Conexão com o BD realizada com sucesso!";
?>