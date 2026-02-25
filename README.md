# Login Project

Projeto organizado em duas partes:

- `backend/`: aplicação Laravel
- `frontend/`: aplicação Vue 3 com Vite

## Backend (Laravel)

```bash
cd backend
composer install
php artisan key:generate
php artisan serve
```

## Frontend (Vue)

```bash
cd frontend
npm install
npm run dev
```

## Observações

- O backend continua com as views Blade existentes.
- O frontend é independente e pode evoluir como SPA consumindo API do Laravel.
