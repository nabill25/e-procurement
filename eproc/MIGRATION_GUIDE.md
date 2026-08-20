# CI 4 + React Migration Guide

## Overview
Full migration of eProcurement system from CodeIgniter 3 (PHP 7.4) to CodeIgniter 4 (PHP 8.2) with React frontend.

## Architecture
- **Backend**: CodeIgniter 4 (PHP 8.2) - REST API
- **Frontend**: React 18 with Tailwind CSS
- **Build Tool**: Vite
- **Database**: MySQL 8.0
- **Container**: Docker + Docker Compose

## Quick Start

### 1. Install Dependencies
```bash
npm install
composer install
```

### 2. Start Development
```bash
docker-compose -f docker-compose.ci4.yml up -d
```

Access:
- Frontend: http://localhost:8080
- Vite Dev Server: http://localhost:5173
- API: http://localhost:8080/api

### 3. Database Setup
```bash
php spark migrate
php spark db:seed SampleSeeder
```

## Project Structure

```
eproc/
├── app/
│   ├── Controllers/        # CI4 controllers (REST API)
│   ├── Models/             # CI4 models
│   ├── Views/              # CI4 views (mostly app.php for React)
│   ├── Config/             # Configuration files
│   └── Database/
│       ├── Migrations/     # Database migrations
│       └── Seeds/          # Database seeders
├── resources/
│   └── js/
│       ├── components/     # React components
│       ├── pages/          # React pages
│       ├── api/            # API client
│       ├── App.jsx         # Main React component
│       └── main.jsx        # React entry point
├── public/
│   ├── build/              # Built React assets (generated)
│   └── uploads/            # User uploads
├── writable/
│   ├── cache/              # Cache directory
│   ├── logs/               # Application logs
│   └── session/            # Session data
├── composer.json           # PHP dependencies
├── package.json            # Node dependencies
├── docker-compose.ci4.yml  # Docker setup
└── Dockerfile.ci4          # Docker image
```

## Migration Checklist

### Phase 1: API Development
- [ ] Create REST endpoints for existing CI3 controllers
- [ ] Implement authentication/authorization
- [ ] Database migration from CI3 to CI4
- [ ] Data transformation utilities

### Phase 2: React Frontend
- [ ] Setup component structure
- [ ] Implement layout and navigation
- [ ] Create page components
- [ ] Integrate API calls

### Phase 3: Testing
- [ ] Unit tests
- [ ] Integration tests
- [ ] E2E tests

### Phase 4: Deployment
- [ ] Production build optimization
- [ ] Environment configuration
- [ ] Deployment pipeline

## Key Technologies

### Backend
- **CodeIgniter 4.4**: Modern PHP framework
- **MySQL 8.0**: Database
- **Composer**: PHP package manager
- **PSR Standards**: PSR-4 autoloading

### Frontend
- **React 18**: UI library
- **React Router**: Navigation
- **Tailwind CSS**: Styling
- **Axios**: HTTP client
- **Vite**: Build tool

## Development Workflow

1. **Backend development**:
   ```bash
   php spark serve --host 0.0.0.0 --port 8000
   ```

2. **Frontend development**:
   ```bash
   npm run dev
   # Runs on http://localhost:5173
   ```

3. **Building for production**:
   ```bash
   npm run build
   composer install --no-dev
   docker build -f Dockerfile.ci4 -t eproc:latest .
   ```

## API Documentation

Base URL: `http://localhost:8080/api/`

### Endpoints

#### Dashboard
- `GET /api/dashboard` - Get dashboard data
- `GET /api/health` - Health check

### Adding New Endpoints

1. Create controller:
```php
<?php
namespace App\Controllers;
use CodeIgniter\API\ResponseTrait;

class Procurement extends BaseController
{
    use ResponseTrait;
    
    public function index()
    {
        return $this->respond(['items' => []]);
    }
}
```

2. Add route in `app/Config/Routes.php`:
```php
$routes->get('procurement', 'Procurement::index');
```

## Database Migrations

Create migration:
```bash
php spark make:migration CreateProcurementTable
```

Example:
```php
<?php
namespace App\Database\Migrations;
use CodeIgniter\Database\Migration;

class CreateProcurementTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'title' => ['type' => 'VARCHAR', 'constraint' => 255],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('procurement');
    }

    public function down()
    {
        $this->forge->dropTable('procurement');
    }
}
```

Run migrations:
```bash
php spark migrate
```

## Troubleshooting

### Port Already in Use
```bash
docker ps
docker kill <container_id>
```

### Database Connection Error
```bash
docker-compose -f docker-compose.ci4.yml logs db
```

### React Not Loading
1. Check Vite dev server is running (port 5173)
2. Check build output in public/build/
3. Verify app/Views/app.php has correct script src

## Next Steps

1. Migrate existing CI3 models to CI4
2. Create API endpoints for all existing features
3. Build React UI for each feature
4. Test thoroughly before deployment
5. Setup CI/CD pipeline

---

**Last Updated**: 2026-08-18  
**Version**: 1.0.0  
**Status**: Development
