# 🔑 API de Recuperação de Senha  

API simples para recuperação de senha via código enviado por e-mail usando **Brevo**.  

## 🚀 Como Usar  

### 📩 1. Solicitar Código de Recuperação  
**Método:** `GET`  
GET http://seuservidor/api.php?action=forgot_password&email=seu@email.com

📌 **Parâmetro:**  
- `email` → O e-mail cadastrado na base de dados.  

📨 **Resposta:**  
```json
{"message": "Código gerado e enviado para seu@email.com"}
```
🔄 2. Redefinir Senha
Método: GET

GET http://seuservidor/api.php?action=reset_password&email=seu@email.com&code=ABC123&new_password=novasenha
📌 Parâmetros:

email → O e-mail cadastrado.
code → Código recebido no e-mail.
new_password → Nova senha desejada.
🔓 Resposta:
✅ Sucesso
```json
{"message": "Senha redefinida com sucesso"}
```
❌ Erro
```json
{"error": "Código inválido ou expirado"}
```
🛠 Tecnologias Usadas
PHP (Backend)
MySQL (Banco de dados)
Brevo (Envio de e-mails)

Codigo MySql
```sql
CREATE TABLE `usuarios` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `username` VARCHAR(30) NOT NULL,
  `email` VARCHAR(255) NOT NULL,
  `password` VARCHAR(255) NOT NULL,
  `saldo` DECIMAL(10, 2) DEFAULT 0.00,
  `admin` TINYINT(1) DEFAULT 0,
  `reset_code` VARCHAR(10) DEFAULT NULL,
  `code_expires_at` DATETIME DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
```

Feito com ❤️ por Raul 🚀
