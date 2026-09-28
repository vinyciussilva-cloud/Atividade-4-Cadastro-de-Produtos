<?php
// 1. O código PHP de processamento deve ficar ANTES de qualquer HTML
$mensagem = "";

// Verifica se veio um status de sucesso via requisição GET (após o redirecionamento)
if (isset($_GET['status']) && $_GET['status'] === 'sucesso') {
    $mensagem = "<p style='color: darkgreen;'>Produto cadastrado com sucesso!</p>";
}

// Verifica se o formulário foi enviado via POST
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Recebe e limpa espaços vazios nas pontas dos campos
    $nome_produto = trim($_POST["nome_produto"] ?? '');
    $preco = trim($_POST["preco"] ?? '');

    // Valida no back-end se os campos foram preenchidos
    if (!empty($nome_produto) && !empty($preco)) {
        $servername = "localhost";
        $username = "root";
        $password = "Senai@118";
        $dbname = "exercicio";

        // Conecta com o banco de dados
        $conn = new mysqli($servername, $username, $password, $dbname);

        // Verifica a conexão
        if ($conn->connect_error) {
            die("Conexão falhou: " . $conn->connect_error);
        }

        // Usa Prepared Statement para prevenir SQL Injection
        $stmt = $conn->prepare("INSERT INTO produtos (nome, preco) VALUES (?, ?)");
        $stmt->bind_param("sd", $nome_produto, $preco);

        if ($stmt->execute()) {
            // Fecha conexões antes de redirecionar
            $stmt->close();
            $conn->close();

            // Redireciona para a mesma página via GET (limpa os dados do POST do navegador)
            header("Location: " . $_SERVER['PHP_SELF'] . "?status=sucesso");
            exit();
        } else {
            $mensagem = "<p style='color: red;'>Erro ao cadastrar no banco de dados.</p>";
        }

        $stmt->close();
        $conn->close();
    } else {
        $mensagem = "<p style='color: red;'>Por favor, preencha todos os campos!</p>";
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro de Produtos</title>
</head>
<body>

    <h1>Cadastro de Produtos</h1>

    <!-- Exibe a mensagem de retorno (sucesso ou erro) -->
    <?php if (!empty($mensagem)) { echo $mensagem; } ?>

    <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="post">
        <label for="nome_produto">Nome do Produto: </label><br>
        <input type="text" id="nome_produto" name="nome_produto" required><br><br>

        <label for="preco">Preço: </label><br>
        <input type="number" id="preco" name="preco" step="0.01" min="0.01" required><br><br>

        <button type="submit">Cadastrar</button>
    </form>

</body>
</html>