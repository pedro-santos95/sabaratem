# SabaraTem

Vitrine virtual em PHP puro + MySQL.

## Deploy na Hostinger (Opcao B - arquivos public na raiz)
1. Envie todo o projeto para `public_html/`.
2. Os arquivos publicos ficam na **raiz** (`index.php`, `produto.php`, `loja.php`, `lojistas.php`, `sobre.php`, `products.php`, `search.php`).
3. Garanta que as pastas `app/`, `admin/`, `assets/` e `includes/` estejam dentro de `public_html/`.
4. Importe o `database.sql` no MySQL.
5. Configure variaveis de ambiente:
   - `DB_HOST`, `DB_NAME`, `DB_USER`, `DB_PASSWORD`, `DB_CHARSET`

## Observacoes
- O admin usa login via tabela `admin_users`.
- Categorias aceitam SVG no campo `svg`.
