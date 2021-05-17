<?php
include('assets/funcoes.php');
if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    $cliente = isset($_GET['nomeCliente']) ? $_GET['nomeCliente'] : '';
    $dataInicio = isset($_GET['dataInicio']) ? $_GET['dataInicio'] : '';
    $dataFim = isset($_GET['dataFim']) ? $_GET['dataFim'] : '';
    $acao = isset($_GET['acao']) ? $_GET['acao'] : '';
    if ($acao == 'limpar') {
        header('location:home.php');
    }
    if ($acao == 'excluir') {
        $id = isset($_GET['id']) ? $_GET['id'] : '';
        $sqlDados = "DELETE FROM anuncios WHERE id = :id;";
        $stmtDados = preparaComando($sqlDados);
        $bindDados = array (
            ':id' => $id
        );
        $stmtDados = bindExecute($stmtDados, $bindDados);
        $dados = $stmtDados->fetch(PDO::FETCH_ASSOC);
        header('location:home.php');
    }
    $pagina = file_get_contents('home.html');
    $lista = apresentaLista($cliente, $dataInicio, $dataFim);
    $pagina = str_replace('{lista}', $lista, $pagina);
    $pagina = str_replace('{nomeCliente}', $cliente, $pagina);
    $pagina = str_replace('{dataInicio}', $dataInicio, $pagina);
    $pagina = str_replace('{dataFim}', $dataFim, $pagina);
    print($pagina);
}else if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $cliente = isset($_POST['nomeCliente']) ?  mb_strtoupper($_POST['nomeCliente'],'UTF-8') : '';
    $dataInicio = isset($_POST['dataInicio']) ? $_POST['dataInicio'] : '';
    $dataFim = isset($_POST['dataFim']) ? $_POST['dataFim'] : '';
    $anuncio = isset($_POST['nomeAnuncio']) ? mb_strtoupper($_POST['nomeAnuncio'],'UTF-8') : '';
    $reais = isset($_POST['reais']) ? $_POST['reais'] : '';
    $acao = isset($_POST['acao']) ? $_POST['acao'] : '';

    if ($acao == 'cancelar') {
        header('location:home.php');
    }

    $sql = 'SELECT COUNT(*) FROM anuncios';
    $stmt = preparaComando($sql);
    $bind = array();
    $stmt = bindExecute($stmt, $bind);
    $count1 = $stmt->fetch(PDO::FETCH_ASSOC);
    $sql = 'INSERT INTO anuncios(nomeAnuncio, nomeCliente, dataInicio, dataFim, reais) VALUES (:nomeAnuncio, :nomeCliente, :dataInicio, :dataFim, :reais)';
    $stmt = preparaComando($sql);
    $bind = array(
        ':nomeAnuncio' => $anuncio,
        ':nomeCliente' => $cliente,
        ':dataInicio' => $dataInicio,
        ':dataFim' => $dataFim,
        ':reais' => $reais
    );
    $stmt = bindExecute($stmt, $bind);
    $sql = 'SELECT COUNT(*) FROM anuncios';
    $stmt = preparaComando($sql);
    $bind = array();
    $stmt = bindExecute($stmt, $bind);
    $count2 = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($count1 < $count2) {
        header('location:home.php');
    }else{
        header('location:add.php');
    }
}
else {
    header('location:home.php');
}


?>