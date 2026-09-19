# Guia de Hospedagem do YouSay na Vercel

Este guia explica como colocar a plataforma **YouSay** (Laravel 11) no ar na **Vercel** em poucos minutos.

---

## 🚀 Passo a Passo Rápido

### 1. Enviar as alterações para o GitHub
Certifique-se de que o código está atualizado no seu repositório:
```bash
git add .
git commit -m "chore: preparar aplicacao para hospedagem na Vercel"
git push origin main
```

---

### 2. Importar o Projecto na Vercel
1. Aceda a [vercel.com](https://vercel.com) e inicie sessão com a sua conta GitHub.
2. Clique em **"Add New..."** > **"Project"**.
3. Selecione o repositório **`bragadossantos/You-say`** e clique em **"Import"**.
4. Nas configurações do projeto:
   - **Framework Preset**: Deixe como **`Other`** (o ficheiro `vercel.json` configurará tudo automaticamente).
   - **Root Directory**: `./` (padrão).
   - **Build and Output Settings**: Pode deixar como está.

---

### 3. Configurar as Variáveis de Ambiente na Vercel
Antes de clicar em *Deploy*, abra a secção **Environment Variables** e adicione as seguintes variáveis:

#### Variáveis Obrigatórias:
| Variável | Valor Recomendado | Descrição |
| :--- | :--- | :--- |
| `APP_NAME` | `YouSay` | Nome da plataforma |
| `APP_ENV` | `production` | Ambiente de produção |
| `APP_KEY` | `base64:YQIVvJZdc+Q5iRdwCrp2D4XseJ+raTIPb1KJJpZS/no=` | Chave de encriptação da app |
| `APP_DEBUG` | `false` | Desativa mensagens de debug públicas |
| `APP_URL` | `https://o-seu-projeto.vercel.app` | O URL gerado pela Vercel |

---

### 4. Opções de Base de Dados

#### Opção A: Demonstração Imediata (SQLite no `/tmp`) — *Zero Configuração*
Se não quiser configurar nenhuma base de dados externa agora, **não adicione nenhuma variável de banco de dados**.
- A nossa configuração do `api/index.php` criará automaticamente uma base SQLite no `/tmp` e executará as migrações e sementes iniciais.
- *(Nota: Como as instâncias serverless são efémeras, novos artigos adicionados podem reiniciar quando a instância for reciclada).*

#### Opção B: Produção com Base de Dados Gratuita na Nuvem (Recomendado)
Para persistência permanente de dados, crie uma base de dados gratuita na nuvem:

##### Com PostgreSQL (ex: [Neon.tech](https://neon.tech) ou [Supabase](https://supabase.com)):
Adicione nas Environment Variables da Vercel:
```ini
DB_CONNECTION=pgsql
DB_HOST=seu-host-neon.tech
DB_PORT=5432
DB_DATABASE=neondb
DB_USERNAME=seu_usuario
DB_PASSWORD=sua_senha
DB_SSLMODE=require
```

##### Com MySQL (ex: [Aiven](https://aiven.io) ou [PlanetScale](https://planetscale.com)):
Adicione nas Environment Variables da Vercel:
```ini
DB_CONNECTION=mysql
DB_HOST=seu-host-mysql
DB_PORT=3306
DB_DATABASE=nome_da_base
DB_USERNAME=seu_usuario
DB_PASSWORD=sua_senha
```

---

### 5. Fazer Deploy
Clique no botão **"Deploy"**. A Vercel irá:
1. Descarregar o código.
2. Usar o runtime PHP com Node 18.
3. Instalar as dependências do Composer.
4. Gerar o seu link público seguro com SSL (ex: `https://you-say.vercel.app`).

---

## 🔑 Contas Pré-criadas
Ao iniciar com o banco semeado, terá acesso a:
- **Admin**: `admin@artigos.local` | Senha: `password`
- **Utilizador**: `braga@artigos.local` | Senha: `password`
