# Kirby Setup
An opinionated setup for Kirby CMS projects.

## Requirements
- PHP 8.4
- Node.js 20

## Installation via Composer
```
composer create-project lukaskleinschmidt/kirby-setup kirby-setup
```

## Install Dependencies
```
npm install
```

### Create a certificate for local development:
```
mkcert -key-file storage/key.pem -cert-file storage/cert.pem 'localhost'
``` 

### Start the development server:
```
npm run dev
```

### Build assets:
```
npm run build
```
