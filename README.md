# 📘 Teacher Platform - Plataforma Educativa Inteligente

Una plataforma educativa avanzada desarrollada con Laravel, Filament y tecnologías de IA para ayudar a estudiantes a organizar, analizar y estudiar su material académico de forma inteligente.

---

## ✨ Características Implementadas

### ETAPA 1: Fundación del Proyecto ✅

- **Laravel 11** - Framework PHP moderno y robusto
- **Filament Admin Panel v3** - Panel de administración completo y personalizable
- **Base de datos MySQL/MariaDB** - Almacenamiento relacional optimizado
- **Sistema de autenticación** - Login seguro y gestión de sesiones
- **Roles y permisos** (Spatie Permission):
  - **Admin**: Acceso completo a todas las funcionalidades
  - **Estudiante**: Acceso limitado a sus propios contenidos
- **Tema personalizado**:
  - Color principal verde (#10B981)
  - Modo oscuro habilitado
  - Interfaz limpia y enfocada en el bienestar visual

### ETAPA 2: Gestión de Contenido ✅

#### Modelos y Estructura de Base de Datos

**1. Asignaturas (Subjects)**
- Organización de materias por estudiante
- Personalización con colores e íconos
- Soft deletes para recuperación de datos

**2. Temas (Topics)**
- Organización jerárquica dentro de cada asignatura
- Sistema de orden personalizable
- Seguimiento de progreso (completado/pendiente)

**3. Material (Materials)**
- Soporte para múltiples tipos: documentos, imágenes, PDFs, enlaces, notas
- Sistema de almacenamiento de archivos
- Campos preparados para OCR e IA:
  - `extracted_text`: Texto extraído por OCR
  - `ai_metadata`: Metadata generada por IA (tags, clasificaciones)
  - `is_processed`: Estado de procesamiento
- Relacionado con asignaturas y temas

#### Recursos Filament
- CRUD completo para Asignaturas
- CRUD completo para Temas
- CRUD completo para Material
- Interfaz administrativa intuitiva

---

## 🚀 Instalación y Configuración

### Requisitos Previos

- PHP 8.2 o superior
- Composer
- MySQL 5.7+ o MariaDB 10.3+
- Node.js y NPM (para assets)

### Pasos de Instalación

1. **Clonar el repositorio**
```bash
git clone https://github.com/pitiflautico/teacher.git
cd teacher
```

2. **Instalar dependencias**
```bash
composer install
npm install
```

3. **Configurar entorno**
```bash
cp .env.example .env
php artisan key:generate
```

4. **Configurar base de datos**

Edita el archivo `.env`:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=teacher_platform
DB_USERNAME=root
DB_PASSWORD=tu_password
```

5. **Ejecutar migraciones y seeders**
```bash
php artisan migrate --seed
```

6. **Generar assets**
```bash
npm run build
```

7. **Iniciar servidor**
```bash
php artisan serve
```

Accede al panel admin en: **http://localhost:8000/admin**

---

## 👤 Usuarios Demo

Usuarios creados automáticamente por los seeders:

### Administrador
- **Email**: admin@teacher.com
- **Password**: admin123
- **Rol**: Admin
- **Permisos**: Acceso completo

### Estudiante
- **Email**: estudiante@teacher.com
- **Password**: estudiante123
- **Rol**: Estudiante
- **Permisos**: Gestión de su propio contenido

---

## 📂 Estructura del Proyecto

```
teacher/
├── app/
│   ├── Filament/
│   │   └── Resources/         # Recursos CRUD de Filament
│   │       ├── MaterialResource.php
│   │       ├── SubjectResource.php
│   │       └── TopicResource.php
│   ├── Models/
│   │   ├── Material.php       # Modelo de Material
│   │   ├── Subject.php        # Modelo de Asignatura
│   │   ├── Topic.php          # Modelo de Tema
│   │   └── User.php           # Modelo de Usuario
│   └── Providers/
│       └── Filament/
│           └── AdminPanelProvider.php
├── database/
│   ├── migrations/            # Migraciones de base de datos
│   └── seeders/
│       ├── RoleSeeder.php     # Roles y permisos
│       └── AdminUserSeeder.php # Usuarios demo
└── config/
    └── permission.php         # Configuración de permisos
```

---

## 🎯 Próximas Etapas

### ETAPA 3: Procesamiento con IA (Próximamente)
- [ ] Integración con OCR (Tesseract/AWS Textract)
- [ ] Extracción automática de texto de imágenes
- [ ] Sistema de colas para procesamiento asíncrono
- [ ] Capa de abstracción para proveedores de IA
- [ ] Gestión de tokens IA

### ETAPA 4: Generación de Ejercicios
- [ ] Conexión con APIs de IA (OpenAI, Replicate, Mistral)
- [ ] Generación de ejercicios tipo test
- [ ] Generación de ejercicios de desarrollo
- [ ] Renderizado de fórmulas matemáticas (KaTeX)
- [ ] Adaptación de dificultad según rendimiento

### ETAPA 5: Calendario y Planificación
- [ ] Integración con Google Calendar
- [ ] Timeline de estudio
- [ ] Recordatorios inteligentes
- [ ] Notificaciones push/email

### ETAPA 6: Analytics y Feedback
- [ ] Dashboard de progreso
- [ ] Reportes semanales automáticos
- [ ] Recomendaciones personalizadas
- [ ] Sistema de evaluación automática

---

## 🛠️ Tecnologías Utilizadas

- **Backend**: Laravel 11
- **Admin Panel**: Filament v3
- **Base de datos**: MySQL/MariaDB
- **Autenticación**: Laravel Sanctum
- **Permisos**: Spatie Laravel Permission
- **Frontend**: Livewire 3, Alpine.js, Tailwind CSS
- **Icons**: Heroicons

---

## 📊 Esquema de Base de Datos

### Tablas Principales

**users**
- Sistema de autenticación de Laravel
- Roles y permisos vía Spatie

**subjects**
- Asignaturas del estudiante
- Personalización (colores, íconos)

**topics**
- Temas organizados por asignatura
- Sistema de ordenamiento

**materials**
- Materiales de estudio
- Soporte para archivos y OCR
- Metadata de IA

### Relaciones
```
User (1) ──► (N) Subjects
Subject (1) ──► (N) Topics
Subject (1) ──► (N) Materials
Topic (1) ──► (N) Materials
User (1) ──► (N) Materials
```

---

## 🔐 Sistema de Permisos

### Permisos Disponibles

#### Gestión de Usuarios
- `view_users`
- `create_users`
- `edit_users`
- `delete_users`

#### Gestión de Asignaturas
- `view_subjects`
- `create_subjects`
- `edit_subjects`
- `delete_subjects`

#### Gestión de Material
- `view_materials`
- `create_materials`
- `edit_materials`
- `delete_materials`

#### Gestión de Ejercicios
- `view_exercises`
- `create_exercises`
- `edit_exercises`
- `delete_exercises`
- `generate_exercises`

#### Panel de Administración
- `access_admin_panel`

---

## 📝 Comandos Útiles

```bash
# Crear nuevo usuario admin
php artisan make:filament-user

# Limpiar caché
php artisan cache:clear
php artisan config:clear
php artisan view:clear

# Refrescar base de datos
php artisan migrate:fresh --seed

# Ver rutas
php artisan route:list

# Ejecutar tests
php artisan test
```

---

## 🤝 Contribución

Este proyecto está en desarrollo activo. Las contribuciones son bienvenidas.

---

## 📄 Licencia

Este proyecto está bajo la licencia MIT.

---

## 📧 Contacto

Para preguntas o sugerencias, contacta al equipo de desarrollo.

---

**Desarrollado con ❤️ para mejorar la experiencia educativa**
