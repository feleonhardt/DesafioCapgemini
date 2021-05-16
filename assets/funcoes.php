<?php
function criarConexao(){
    require_once('conf/conf.inc.php');
    try {
        $conexao = new PDO(MYSQL,USER,PASSWORD, array(PDO::MYSQL_ATTR_FOUND_ROWS => true));
        return $conexao;
    } catch (PDOExeption $e) {
        print('Erro ao conectar com o banco de dados. Favor verificar parâmetros!');
        die();
    }catch(Exeption $e){
        print('Erro genérico. Entre em contato com o administrador do site!');
        die();
    }
}

function preparaComando($sql){
    try {
        $conexao = criarConexao();
        $comando = $conexao->prepare($sql);
        return $comando;
    } catch (Execption $e) {
        print('Erro ao preparar o comando!');
        die();
    }
}
 
function bindExecute($comando, &$dados){
    try {
        foreach ($dados as $chave => &$valor) {
            $comando->bindParam($chave, $valor);
        }
        $comando = executaComando($comando);
        return $comando;
    } catch (Execption $e) {
        print('Erro ao realizar bind!');
        die();
    }
}

function executaComando($comando){
    try {
        $comando->execute();
        return $comando;
    } catch (Exeption $e) {
        print('Erro ao executar o comando. '.$e->getMessage());
        die();
    }
}
function formatData($dataInicio){
    $vetData = explode('-', $dataInicio);
    switch ($vetData[1]) {
        case 1:
            $mes = 'Janeiro';
            break;
        case 2:
            $mes = 'Fevereiro';
            break;
        case 3:
            $mes = 'Março';
            break;
        case 4:
            $mes = 'Abril';
            break;
        case 5:
            $mes = 'Maio';
            break;
        case 6:
            $mes = 'Junho';
            break;
        case 7:
            $mes = 'Julho';
            break;
        case 8:
            $mes = 'Agosto';
            break;
        case 9:
            $mes = 'Setembro';
            break;
        case 10:
            $mes = 'Outubro';
            break;
        case 11:
            $mes = 'Novembro';
            break;
        case 12:
            $mes = 'Dezembro';
            break; 
    }
    $data = $vetData[2]." de ".$mes." de ".$vetData[0];
    return $data;

}

function inverteData($data){
    if(count(explode("/",$data)) > 1){
        return implode("-",array_reverse(explode("/",$data)));
    }elseif(count(explode("-",$data)) > 1){
        return implode("/",array_reverse(explode("-",$data)));
    }
}



function apresentaLista($cliente, $dataInicio, $dataFim){
    $lista = '';
    if ($cliente != '' and $dataInicio == '' and $dataFim == '') {
        $sqlDados = "SELECT * FROM anuncios WHERE nomeCliente LIKE :nomeCliente ORDER BY dataInicio;";
        $stmtDados = preparaComando($sqlDados);
        $bindDados = array (
            'nomeCliente' => $cliente."%"
        );
    }else if ($cliente != '' and $dataInicio != '' and $dataFim == '') {
        $sqlDados = "SELECT * FROM anuncios WHERE nomeCliente LIKE :nomeCliente AND dataInicio >= :dataInicio ORDER BY dataInicio;";
        $stmtDados = preparaComando($sqlDados);
        $bindDados = array (
            ':nomeCliente' => $cliente."%",
            ':dataInicio' => $dataInicio
        );
    }else if ($cliente != '' and $dataInicio != '' and $dataFim != '') {
        $sqlDados = "SELECT * FROM anuncios WHERE nomeCliente LIKE :nomeCliente AND (dataInicio >= :dataInicio and dataFim <= :dataFim) OR (dataInicio < :dataInicio and dataFim <= :dataFim) OR (dataInicio >= :dataInicio and dataFim > :dataFim)  ORDER BY dataInicio;";
        $stmtDados = preparaComando($sqlDados);
        $bindDados = array (
            ':nomeCliente' => $cliente."%",
            ':dataInicio' => $dataInicio,
            ':dataFim' => $dataFim
        );
    }else {
        $sqlDados = "SELECT * FROM anuncios ORDER BY dataInicio;";
        $stmtDados = preparaComando($sqlDados);
        $bindDados = array ();
    }
    $stmtDados = bindExecute($stmtDados, $bindDados);
    while ($dados = $stmtDados->fetch(PDO::FETCH_ASSOC)) {
        $lista .= "<hr>";
        $lista .= "<div>";
        $lista .= "<h3>".$dados['nomeAnuncio']."</h3>";
        $lista .= "Cliente: ".$dados['nomeCliente']."<br>";
        $lista .= "Início: ".formatData($dados['dataInicio'])."<br>";
        $lista .= "Fim: ".formatData($dados['dataFim'])."<br>";
        $lista .= "Valor investido: R$ ".number_format($dados['reais'],2,",",".")."<br>";
        $lista .= "<br><br>Relatótio:<br>";

        $data_inicio = new DateTime($dados['dataInicio']);
        $data_fim = new DateTime($dados['dataFim']);
        $dateInterval = $data_inicio->diff($data_fim);
        $dias = $dateInterval->days;

        $vetDados = calculaQnt($dias, $dados['reais']);
        $lista .= "Valor total: R$ ".number_format($vetDados['valorTotal'],2,",",".")."<br>";
        $lista .= "Clique(s): ".number_format($vetDados['clique'],0,",",".")."<br>";
        $lista .= "Compartilhamento(s): ".number_format($vetDados['compartilhamento'],0,",",".")."<br>";
        $lista .= "Visualização(ões): ".number_format($vetDados['visualizaçao'],0,",",".")."<br>";

        // $lista .= ": ".$dados[''];
        // $lista .= "";


        $lista .= "</div>";
        $lista .= "<hr>";
    }
    return $lista;
}

function calculaQnt($dias, $valor){
    $qnt = $dias * $valor;
    $vizOriginal = floor($qnt * 30);
    $totalViz = $vizOriginal;
    $totalClq = 0;
    $totalCmp = 0;
    $vizMultiplicacao = $vizOriginal;

    // Calculo das visualizações nos compartilhamentos.
    for ($i=0; $i < 4; $i++) { 
        $clqCompartilhamento = floor(($vizMultiplicacao * 0.12));
        $cmpCompartilhamento = floor(($clqCompartilhamento * 0.15));
        $VizCompartilhamento = floor(($cmpCompartilhamento * 40));

        $totalViz += $VizCompartilhamento;
        $totalClq += $clqCompartilhamento;
        $totalCmp += $cmpCompartilhamento;
        $vizMultiplicacao = $VizCompartilhamento;
    }
    $totalClq += floor(($vizMultiplicacao * 0.12));
    return array(
        'valorTotal' => $qnt,
        'visualizaçao' => $totalViz,
        'clique' => $totalClq,
        'compartilhamento' => $totalCmp
    );
}
?>