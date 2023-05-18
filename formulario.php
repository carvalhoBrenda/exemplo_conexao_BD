<?php
    //se nome, e-mail e senha não estiverem vazios o comando php é executado
    if(!empty($_POST['nome']) && !empty($_POST['nascimento']) && !empty($_POST['email']) && !empty($_POST['cidade']) && !empty($_POST['filhos']) && !empty($_POST['salario']))
    {
        require_once "conexao.php"; //executa o arquivo conexao
        $conexao = novaConexao(); //variável conexao recebe o comando
    }
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro de Pessoas</title>
    <link rel="stylesheet" href="bootstrap/css/bootstrap.min.css">
    <style>
        body{
            margin-left: 20px;
            margin-top: 20px;
        }
    </style>
</head>

<body>
    <h2>Cadastro</h2>
    <?php
        /* Validação dos Campos*/
        //se array post é maior que zero, usuário fez requisição
       // (clicou no botão enviar)
        if (count($_POST) > 0)
        {
            //cria array para armazenar as msg de erros
            $erros = 0;
            //verifica se usuário digitou o nome
            //fç filter: verifica se o parametro foi passado, verifica se nome NÃO foi preenchido
            if(!filter_input(INPUT_POST,"nome"))
            {
                echo 'Nome é obrigatório','<br>';
                $erros++;
            }
            if(!filter_input(INPUT_POST,"nascimento")) //verifica se data esta setada
            {
                echo 'Data de Nascimento é obrigatório <br>';
                $erros++;
            }
            if(filter_input(INPUT_POST,"nascimento")) //verifica se esta prenchida
            {
                $data = DateTime::createFromFormat('d/m/Y',$_POST['nascimento']);
                if (!$data) //se a variavel não estiver no formato
                {
                    echo'Data deve estar no padrão dd/mm/aaaa' , '<br>';
                    $erros++;
                }
            }
            //fç verifica se e-mail é vazio e se é válido (se tem @)
            if(!filter_var($_POST['email'],FILTER_VALIDATE_EMAIL))
            {
                echo 'E-mail invalido', '<br>';
                $erros++;
            }
            if(!filter_input(INPUT_POST,"cidade"))
            {
                echo 'Cidade é obrigatorio', '<br>';
                $erros++;
            }
            $filhosconfig =["options"=> ["min_range"=>0, "max_range"=>10]];
            //valida campo filho para numeros inteiros de 0-10
            if( !filter_var($_POST['filhos'],FILTER_VALIDATE_INT,$filhosconfig) && $_POST['filhos'] != 0)
            {
                echo'Quantidade de filhos invalida', '<br>';
                $erros++;
            }    
            $salarioconfig = ["options"=> ["decimal"=> ',']];
            //valida campo salario para decimal com virgula
            if(!filter_var($_POST['salario'], FILTER_VALIDATE_FLOAT,$salarioconfig))
            {
                echo'Salario invalido', '<br>';
                $erros++;
            }
            //se não houver erros no formulario
            if($erros == 0)
            {
                require_once "conexao.php"; //executa o arquivo conexao.php
                $conexao = novaConexao();  //variavel conexao recebe o retorno da fç 

                //converte data formato MySQL
                $data = $_POST['nascimento'];
                //explode: divide a data onde houver "/" (dd  mm  aaaa)
                $dataP = explode('/', $data);
                $dataBanco = $dataP[2].'-'.$dataP[1].'-'.$dataP[0]; 

                try
                {
                    //monta a sql
                    $sql = "INSERT INTO pessoa (nome, nascimento, email, cidade, filhos, salario) VALUES (:nome, :nascimento, :email, :cidade, :filhos, :salario)";

                    //metodo prepare: prepara a coomesulnascimentoa
                    //ele não executa diretamente a query, ele aguarda o ok para executar
                    //$stmt (statement) recebe o retorno da instrução sql
                    $stmt = $conexao->prepare($sql);

                    //$_POST['usuario'] após ser tratado internamente pelo método bindValue que remove qualquer injeção SQL
                    $stmt->bindValue(':nome', $_POST['nome']);
                    $stmt->bindValue(':nascimento', $dataBanco);
                    $stmt->bindValue(':email', $_POST['email']);
                    $stmt->bindValue(':cidade', $_POST['cidade']);
                    $stmt->bindValue(':filhos', $_POST['filhos']);
                    $stmt->bindValue(':salario', $_POST['salario']);

                    //executa a sql depois do bindValue
                    $stmt->execute();
                    echo "Registro cadastrado com sucesso!";
                }//fecha o try
                catch (PDOException $e)
                {
                    echo 'Mensagem de erro: ' . $e->getMessage();
                }






            }
        }//fecha o if do count
        ?>
    <form action="#" method="post">
        <div class="form-row">
            <div class="form-group col-md-6">
                <label for="nome">Nome</label>
                <input type="text" class="form-control" id="nome" name="nome" placeholder="Nome"
                value="<?php if (isset($_POST['nome'])){ echo $_POST['nome'];}?>">
            </div>
            <div class="form-group col-md-4">
                <label for="nascimento">Nascimento</label>
                <input type="text" class="form-control" id="nascimento" name="nascimento" placeholder="Nascimento"
                value="<?php if (isset($_POST['nascimento'])){ echo $_POST['nascimento'];}?>">
            </div>
        </div>
        <div class="form-row">
            <div class="form-group col-md-3">
                <label for="email">E-mail</label>
                <input type="text" class="form-control" class="form-control" id="email" name="email" placeholder="E-mail"
                value="<?php if (isset($_POST['email'])){ echo $_POST['email'];}?>">
            </div>
            <div class="form-group col-md-6">
                <label for="cidade">Cidade</label>
                <input type="text" class="form-control" id="cidade" name="cidade" placeholder="cidade"
                value="<?php if (isset($_POST['cidade'])){ echo $_POST['cidade'];}?>">
            </div>
        </div>
        <div class="form-row">
            <div class="form-group col-md-4">
                <label for="filhos">Qtde de Filhos</label>
                <input type="number" class="form-control" class="form-control" id="filhos" name="filhos" placeholder="Qtde de Filhos">
            </div>
            <div class="form-group col-md-4">
                <label for="salario">Salário</label>
                <input type="text" class="form-control" id="salario" name="salario" placeholder="Salário">
            </div>
        </div>
        <button class="btn btn-primary btn-lg">Enviar</button>
    </form>
    
</body>
</html>
