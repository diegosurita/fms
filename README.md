# FMS
Small Fund Management System

## Project structure

- `backend`: Laravel API/application
- `frontend`: Vue 3 + Vite application

## Run with Docker Compose

From the repository root:

```sh
docker compose up -d
```

Applications:

- Frontend: http://localhost
- Backend: http://localhost:8000

## Database ER diagram

```mermaid
erDiagram
	COMPANIES {
		bigint id PK
		varchar name
		datetime created_at
		datetime updated_at
		datetime deleted_at
	}

	FUND_MANAGERS {
		bigint id PK
		varchar name
		datetime created_at
		datetime updated_at
		datetime deleted_at
	}

	FUNDS {
		bigint id PK
		varchar name
		int start_year
		bigint manager_id FK
		datetime created_at
		datetime updated_at
		datetime deleted_at
	}

	FUND_ALIASES {
		bigint id PK
		varchar alias UK
		bigint fund FK
		datetime created_at
		datetime updated_at
	}

	COMPANIES_FUNDS {
		bigint company FK
		bigint fund FK
	}

	DUPLICATED_FUNDS {
		bigint source_fund_id FK
		bigint duplicated_fund_id FK
	}

	FUND_MANAGERS ||--o{ FUNDS : manages
	FUNDS ||--o{ FUND_ALIASES : has_aliases
	COMPANIES ||--o{ COMPANIES_FUNDS : linked_to
	FUNDS ||--o{ COMPANIES_FUNDS : linked_to
	FUNDS ||--o{ DUPLICATED_FUNDS : source
	FUNDS ||--o{ DUPLICATED_FUNDS : duplicate
```
