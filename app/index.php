<?php

// Função para descriptografar dados
function descriptografarAES($textoCifrado, $chave, $iv, $tamanhoChave = 256, $modo = 'CBC') {
    // Verificar o tamanho da chave e ajustar conforme necessário
    if ($tamanhoChave == 128) {
        $algoritmo = 'AES-128-' . $modo;
    } elseif ($tamanhoChave == 256) {
        $algoritmo = 'AES-256-' . $modo;
    } else {
        return false; // Retornar falso se o tamanho da chave não for suportado
    }

    // Descriptografar usando AES
    $textoDescriptografado = openssl_decrypt(base64_decode($textoCifrado), $algoritmo, $chave, OPENSSL_RAW_DATA, $iv);

    return $textoDescriptografado;
}

// Verificar se os dados foram recebidos via POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Receber os dados do POST como JSON
    $dados = json_decode(file_get_contents('php://input'), true);

    // Verificar se os dados necessários foram fornecidos
    if (isset($dados['textoCifrado']) && isset($dados['chave']) && isset($dados['iv']) && isset($dados['modo']) && isset($dados['tamanhoChave'])) {
        // Descriptografar os dados
        $textoDescriptografado = descriptografarAES($dados['textoCifrado'], $dados['chave'], $dados['iv'], $dados['tamanhoChave'], $dados['modo']);

        // Debugging: Log do texto descriptografado
        error_log('Texto descriptografado: ' . $textoDescriptografado);

        // Retornar o texto descriptografado como JSON no response
        header('Content-Type: application/json');
        echo json_encode(['textoDescriptografado' => $textoDescriptografado]);
        exit; // Finalizar a execução após enviar a resposta
    } else {
        // Se algum dado estiver faltando, retornar erro
        http_response_code(400);
        echo json_encode(['erro' => 'Dados incompletos']);
        exit; // Finalizar a execução após enviar a resposta
    }
} else {
    // Se não for uma requisição POST, retornar erro
    http_response_code(405);
    echo json_encode(['erro' => 'Método não permitido']);
    exit; // Finalizar a execução após enviar a resposta
}

?>
