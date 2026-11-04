# YHM API Documentation

## Authentication
- `POST /api/login` - Get token
- `POST /api/logout` - Revoke token

## Cards
- `GET /api/cards` - List cards
- `GET /api/cards/{id}` - Get one card
- `POST /api/cards` - Create card (Admin)
- `POST /api/cards/{id}/activate` - Activate card

## Dashboard
- `GET /api/dashboard` - Overall stats
