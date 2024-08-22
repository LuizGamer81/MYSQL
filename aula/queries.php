<?php

/**
 * Prova de Banco de Dados e PHP - Escola Barriga Verde
 * Professor: Alexandre Rafael Kohler da Silva
 * 
 * Descrição:
 * Esta prova tem como objetivo avaliar a sua capacidade de criar e manipular um banco de dados utilizando MySQL e PHP.
 * Você deve implementar as operações essenciais para a manipulação de dados de usuários e transações bancárias.
 * 
 * Estrutura do Banco de Dados:
 * 
 * Instruções: 
 * 1. Crie um banco de dados chamado `nubank_system`.
 * 2. Use o comando `USE nubank_system;` para trabalhar com este banco.
 * 3. Crie as tabelas `users` e `transactions` usando os comandos SQL abaixo:
 *
 * CREATE DATABASE nubank_system;
 * USE nubank_system;
 * 
 * CREATE TABLE users (
 *     id INT AUTO_INCREMENT PRIMARY KEY,    -- Chave primária única para cada usuário
 *     cpf VARCHAR(11) UNIQUE NOT NULL,      -- CPF do usuário, deve ser único e não nulo
 *     name VARCHAR(100) NOT NULL,           -- Nome do usuário, obrigatório
 *     password VARCHAR(255) NOT NULL,       -- Senha do usuário, deve ser armazenada em formato hash
 *     balance DECIMAL(15, 2) DEFAULT 0.00   -- Saldo inicial do usuário, padrão é 0.00
 * );
 * 
 * CREATE TABLE transactions (
 *     id INT AUTO_INCREMENT PRIMARY KEY,    -- Chave primária única para cada transação
 *     user_id INT NOT NULL,                 -- ID do usuário que realizou a transação
 *     type ENUM('deposit', 'withdraw', 'transfer', 'pix') NOT NULL, -- Tipo da transação
 *     amount DECIMAL(15, 2) NOT NULL,       -- Valor da transação
 *     created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP, -- Data e hora da transação
 *     FOREIGN KEY (user_id) REFERENCES users(id)      -- Chave estrangeira referenciando o usuário
 * );
 */

/**
 * Função 1: registerUser
 * 
 * Descrição: 
 * Esta função é usada para registrar um novo usuário no sistema. 
 * Ela insere o nome, CPF e senha (hashada) do usuário na tabela `users`.
 * 
 * SQL Usada:
 *     INSERT INTO users (name, cpf, password) 
 *     VALUES ('$name', '$cpf', '$password');
 *
 * Exemplo de uso:
 * $user_id = registerUser($conn, 'João Silva', '12345678901', password_hash('senha123', PASSWORD_BCRYPT));
 * if ($user_id) {
 *     echo "Usuário registrado com sucesso! ID: $user_id";
 * } else {
 *     echo "Erro ao registrar usuário.";
 * }
 */
function registerUser($conn, $name, $cpf, $password) {
    $sql = "insert into users (name, cpf, password) values ('$name', '$cpf', '$password')" ;
    return mysqli_query($conn, $sql) ? mysqli_insert_id($conn) : false;
}

/**
 * Função 2: getUserByCPF
 * 
 * Descrição: 
 * Esta função busca um usuário no banco de dados com base no CPF. 
 * Ela retorna todos os dados do usuário se o CPF for encontrado.
 * 
 * SQL Usada:
 *     SELECT * FROM users WHERE cpf='$cpf';
 *
 * Exemplo de uso:
 * $result = getUserByCPF($conn, '12345678901');
 * if (mysqli_num_rows($result) > 0) {
 *     $user = mysqli_fetch_assoc($result);
 *     print_r($user);
 * } else {
 *     echo "Usuário não encontrado.";
 * }
 */
function getUserByCPF($conn, $cpf) {
    $sql = "SELECT * FROM users WHERE cpf='$cpf'";
    return mysqli_query($conn, $sql);
}

/**
 * Função 3: updateBalance
 * 
 * Descrição: 
 * Esta função é usada para atualizar o saldo de um usuário. 
 * Dependendo da operação (`deposit` ou `withdraw`), 
 * ela aumenta ou diminui o saldo do usuário na tabela `users`.
 * 
 * SQL Usada:
 * Para depósito:
 *     UPDATE users SET balance = balance + $amount WHERE id = $user_id;
 * 
 * Para saque:
 *     UPDATE users SET balance = balance - $amount WHERE id = $user_id;
 *
 * Exemplo de uso:
 * if (updateBalance($conn, 1, 500.00, 'deposit')) {
 *     echo "Saldo atualizado com sucesso!";
 * } else {
 *     echo "Erro ao atualizar saldo.";
 * }
 */
function updateBalance($conn, $user_id, $amount, $operation) {
    $operator = $operation === 'deposit' ? '+' : '-';
    $sql = "update users set balance = balance $operator $amount where id = $user_id";
    return mysqli_query($conn, $sql);
}

/**
 * Função 4: insertTransaction
 * 
 * Descrição: 
 * Esta função registra uma nova transação na tabela `transactions`. 
 * Ela armazena o ID do usuário, o tipo de transação e o valor envolvido.
 * 
 * SQL Usada:
 *     INSERT INTO transactions (user_id, type, amount) 
 *     VALUES ($user_id, '$type', $amount);
 *
 * Exemplo de uso:
 * if (insertTransaction($conn, 1, 'deposit', 500.00)) {
 *     echo "Transação registrada com sucesso!";
 * } else {
 *     echo "Erro ao registrar transação.";
 * }
 */
function insertTransaction($conn, $user_id, $type, $amount) {
    $sql = "insert into transactions (user_id, type, amount) values ('$user_id', '$type', $amount)";
    return mysqli_query($conn, $sql);
}

/**
 * Função 5: getUserBalance
 * 
 * Descrição: 
 * Esta função retorna o saldo atual de um usuário baseado no ID do usuário.
 * Ela busca o saldo na tabela `users` e retorna o valor atual.
 * 
 * SQL Usada:
 *     SELECT balance FROM users WHERE id = $user_id;
 *
 * Exemplo de uso:
 * $result = getUserBalance($conn, 1);
 * if ($result) {
 *     $row = mysqli_fetch_assoc($result);
 *     echo "Saldo: " . $row['balance'];
 * } else {
 *     echo "Erro ao obter saldo.";
 * }
 */
function getUserBalance($conn, $user_id) {
    $sql = "select balance from users where id = $user_id";
    return mysqli_query($conn, $sql);
}

/**
 * Função 6: getUserIdByCPF
 * 
 * Descrição: 
 * Esta função retorna o ID de um usuário baseado no CPF fornecido.
 * Ela busca o ID na tabela `users` e retorna o valor se o CPF for encontrado.
 * 
 * SQL Usada:
 *     SELECT id FROM users WHERE cpf = '$cpf';
 *
 * Exemplo de uso:
 * $result = getUserIdByCPF($conn, '12345678901');
 * if (mysqli_num_rows($result) > 0) {
 *     $row = mysqli_fetch_assoc($result);
 *     echo "ID do usuário: " . $row['id'];
 * } else {
 *     echo "Usuário não encontrado.";
 * }
 */
function getUserIdByCPF($conn, $cpf) {
    $sql = "select id from users where cpf = '$cpf'";
    return mysqli_query($conn, $sql);
}

?>
