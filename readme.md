🔑 API de Recuperação de Senha
API simples para recuperação de senha via código enviado por e-mail usando Brevo.

🚀 Como Usar
📩 1. Solicitar Código de Recuperação
Método: GET

bash
Copiar
Editar
GET http://seuservidor/api.php?action=forgot_password&email=seu@email.com
📌 Parâmetro:

email → O e-mail cadastrado na base de dados.
📨 Resposta:

json
Copiar
Editar
{"message": "Código gerado e enviado para seu@email.com"}
🔄 2. Redefinir Senha
Método: GET

bash
Copiar
Editar
GET http://seuservidor/api.php?action=reset_password&email=seu@email.com&code=ABC123&new_password=novasenha
📌 Parâmetros:

email → O e-mail cadastrado.
code → Código recebido no e-mail.
new_password → Nova senha desejada.
🔓 Resposta:
✅ Sucesso

json
Copiar
Editar
{"message": "Senha redefinida com sucesso"}
❌ Erro

json
Copiar
Editar
{"error": "Código inválido ou expirado"}
🛠 Tecnologias Usadas
PHP (Backend)
MySQL (Banco de dados)
Brevo (Envio de e-mails)
📌 Observação: Certifique-se de configurar corretamente a API da Brevo no código.

Feito com ❤️ por Raul 🚀

