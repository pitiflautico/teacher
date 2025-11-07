# 📘 Teacher Platform - Plataforma Educativa Inteligente con IA

Una plataforma educativa avanzada desarrollada con Laravel 11, Filament v3.3 y tecnologías de IA para ayudar a estudiantes a organizar, analizar y estudiar su material académico de forma inteligente.

---

## ✨ Características Implementadas

### ETAPA 1: Fundación del Proyecto ✅

- **Laravel 11** - Framework PHP moderno y robusto
- **Filament Admin Panel v3.3** - Panel de administración completo y personalizable
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
- Procesamiento OCR integrado
- Metadata generada por IA

#### Recursos Filament
- CRUD completo para Asignaturas
- CRUD completo para Temas
- CRUD completo para Material
- Interfaz administrativa intuitiva

### ETAPA 3-4: Sistema de IA Multi-Proveedor ✅

#### Arquitectura de IA
- **Capa de abstracción** para múltiples proveedores de IA
- **Gestión automática de tokens** y límites mensuales
- **Tracking de uso** con estadísticas detalladas
- **Soporte para múltiples proveedores**:
  - OpenAI (GPT-4o-mini)
  - Replicate (Llama 2)
  - Together.ai (Llama 3.1)

#### Procesamiento OCR
- **Sistema OCR** con Tesseract para extracción de texto
- **Procesamiento asíncrono** con Laravel Queues
- **Soporte multi-idioma** (español, inglés)
- **Detección automática** de contenido matemático
- **Limpieza y normalización** de texto extraído

#### Generación de Ejercicios
- **5 tipos de ejercicios** soportados:
  - Opción múltiple (multiple_choice)
  - Verdadero/Falso (true_false)
  - Respuesta corta (short_answer)
  - Ensayo (essay)
  - Resolución de problemas (problem_solving)
- **3 niveles de dificultad**: easy, medium, hard
- **Generación contextual** basada en material procesado
- **Soporte para LaTeX** en preguntas matemáticas
- **Metadata de IA** incluida en cada ejercicio

### ETAPA 5-7: Sistema Completo de Aprendizaje ✅

#### Sistema de Estudiantes
- **Página de ejercicios** (TakeExercise) para estudiantes
- **Tracking de intentos** con ExerciseAttempt
- **Calificación automática** de respuestas
- **Historial de progreso** por ejercicio

#### Dashboard de Analytics
- **StudentStatsOverview** - Estadísticas del estudiante:
  - Total de ejercicios completados
  - Promedio de calificaciones
  - Tiempo total de estudio
  - Racha actual de estudio
- **TeacherStatsOverview** - Estadísticas del profesor:
  - Total de materiales subidos
  - Ejercicios generados
  - Estudiantes activos
  - Tasa de procesamiento OCR
- **ProgressBySubjectChart** - Gráfico de progreso por materia:
  - Últimos 7 días de actividad
  - Visualización con Chart.js

#### Sistema de Notificaciones
- **MaterialProcessedNotification** - Notifica cuando OCR termina
- **ExercisesGeneratedNotification** - Notifica ejercicios nuevos
- **LowTokensWarning** - Alerta de tokens bajos
- **Canales**: Database + Email
- **Centro de notificaciones** en Filament

#### Sistema de Flashcards con Spaced Repetition (SM-2)
- **Algoritmo SM-2** (SuperMemo) implementado:
  - Factor de facilidad (easiness_factor)
  - Intervalo de repetición (interval)
  - Contador de repeticiones (repetitions)
  - Próxima revisión (next_review_at)
- **Sistema de rating** 0-5:
  - 0: Blackout completo
  - 1: Incorrecto pero familiar
  - 2: Incorrecto pero fácil de recordar
  - 3: Correcto pero difícil
  - 4: Correcto con hesitación
  - 5: Recall perfecto
- **Tracking de revisiones** con FlashcardReview
- **Estadísticas de rendimiento**:
  - Total de revisiones
  - Revisiones correctas
  - Racha actual
  - Porcentaje de precisión
- **Interfaz Filament completa**:
  - Creación/edición de flashcards
  - Filtros por materia/tema
  - Filtro de "due for review"
  - Acción de reset

#### Audit Logs
- **Sistema de auditoría** para compliance
- **Tracking de eventos**: created, updated, deleted, accessed
- **Metadata capturada**:
  - Usuario responsable
  - Valores antiguos/nuevos (JSON)
  - IP address
  - User agent
  - Timestamp

---

## 🚀 Instalación y Configuración

### Requisitos Previos

- PHP 8.2 o superior
- Composer
- MySQL 5.7+ o MariaDB 10.3+
- Node.js y NPM (para assets)
- Tesseract OCR (opcional, para procesamiento de imágenes)
- Redis (opcional, para queues)

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

5. **Configurar proveedores de IA** (opcional)

```env
# OpenAI
OPENAI_API_KEY=tu_api_key_aqui
OPENAI_MODEL=gpt-4o-mini
OPENAI_MONTHLY_LIMIT=1000000

# Replicate
REPLICATE_API_KEY=tu_api_key_aqui

# Together.ai
TOGETHER_API_KEY=tu_api_key_aqui

# Proveedor por defecto
AI_DEFAULT_PROVIDER=openai
```

6. **Configurar OCR** (opcional)

```env
OCR_DEFAULT_PROVIDER=tesseract
OCR_LANGUAGE=spa+eng
TESSERACT_PATH=/usr/bin/tesseract
```

7. **Ejecutar migraciones y seeders**
```bash
php artisan migrate --seed
# Crear datos demo (opcional)
php artisan db:seed --class=DemoDataSeeder
```

8. **Generar assets**
```bash
npm run build
```

9. **Iniciar servicios**
```bash
# Servidor Laravel
php artisan serve

# Queue worker (en otra terminal)
php artisan queue:work
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

## 🧪 Testing y Navegación

### 1. Iniciar sesión como Admin

1. Navega a http://localhost:8000/admin
2. Inicia sesión con admin@teacher.com / admin123
3. Verás el dashboard con widgets de estadísticas

### 2. Crear una Asignatura

1. Click en "Subjects" en el menú lateral
2. Click en "New Subject"
3. Completa el formulario:
   - Name: "Matemáticas"
   - Description: "Curso de matemáticas avanzadas"
   - Color: Selecciona un color (#3B82F6)
   - Icon: "calculator"
4. Click en "Create"

### 3. Crear Topics

1. Click en "Topics" en el menú
2. Click en "New Topic"
3. Selecciona la asignatura creada
4. Completa: Name, Description, Order
5. Click en "Create"

### 4. Subir Material con OCR

1. Click en "Materials"
2. Click en "New Material"
3. Completa el formulario:
   - Subject: Selecciona una asignatura
   - Topic: Selecciona un tema
   - Title: "Introducción a Álgebra"
   - Type: PDF/Image
   - File: Sube un archivo
4. El sistema procesará el archivo con OCR automáticamente
5. Recibirás una notificación cuando termine

### 5. Generar Ejercicios

1. Ve a "Materials"
2. Selecciona un material procesado
3. Click en el botón "Generate Exercises"
4. Configura:
   - Exercise Type: multiple_choice
   - Difficulty: medium
   - Count: 5
5. Los ejercicios se generarán asíncronamente
6. Recibirás una notificación cuando estén listos

### 6. Crear Flashcards

1. Click en "Flashcards"
2. Click en "New Flashcard"
3. Completa:
   - Front: "¿Qué es una matriz?"
   - Back: "Un arreglo rectangular de números..."
   - Hint: (opcional)
   - Subject y Topic
4. La flashcard se inicializa con SM-2:
   - easiness_factor: 2.5
   - interval: 0
   - next_review_at: now()

### 7. Revisar Flashcards

1. Ve a "Flashcards"
2. Filtra por "Due for Review"
3. Selecciona una flashcard
4. Califica tu respuesta (0-5)
5. El algoritmo SM-2 actualiza automáticamente:
   - Próxima fecha de revisión
   - Intervalo de repetición
   - Factor de facilidad

### 8. Ver Analytics

1. El dashboard muestra:
   - Estadísticas generales
   - Gráfico de progreso por materia (últimos 7 días)
   - Estadísticas de ejercicios
2. Los widgets se actualizan en tiempo real

### 9. Comprobar Notificaciones

1. Click en el icono de campana (arriba derecha)
2. Verás notificaciones de:
   - Materiales procesados
   - Ejercicios generados
   - Tokens bajos

---

## 📂 Estructura del Proyecto

```
teacher/
├── app/
│   ├── Filament/
│   │   ├── Pages/
│   │   │   └── TakeExercise.php        # Página de ejercicios para estudiantes
│   │   ├── Resources/
│   │   │   ├── ExerciseAttemptResource.php
│   │   │   ├── ExerciseResource.php
│   │   │   ├── FlashcardResource.php
│   │   │   ├── MaterialResource.php
│   │   │   ├── SubjectResource.php
│   │   │   └── TopicResource.php
│   │   └── Widgets/
│   │       ├── ProgressBySubjectChart.php
│   │       ├── StudentStatsOverview.php
│   │       └── TeacherStatsOverview.php
│   ├── Jobs/
│   │   ├── GenerateExercises.php       # Job asíncrono de generación
│   │   └── ProcessMaterialWithOCR.php  # Job asíncrono de OCR
│   ├── Models/
│   │   ├── AuditLog.php
│   │   ├── Exercise.php
│   │   ├── ExerciseAttempt.php
│   │   ├── Flashcard.php
│   │   ├── FlashcardReview.php
│   │   ├── Material.php
│   │   ├── Subject.php
│   │   ├── TokenUsage.php
│   │   ├── Topic.php
│   │   └── User.php
│   ├── Notifications/
│   │   ├── ExercisesGeneratedNotification.php
│   │   ├── LowTokensWarning.php
│   │   └── MaterialProcessedNotification.php
│   └── Services/
│       ├── AI/
│       │   ├── AIManager.php           # Gestor de proveedores IA
│       │   ├── AIProviderInterface.php
│       │   ├── ExerciseGenerator.php   # Generador de ejercicios
│       │   ├── OpenAIProvider.php
│       │   ├── ReplicateProvider.php
│       │   └── TogetherAIProvider.php
│       └── OCR/
│           ├── OCRManager.php          # Gestor de proveedores OCR
│           ├── OCRProviderInterface.php
│           └── TesseractProvider.php
├── database/
│   ├── factories/
│   │   ├── ExerciseFactory.php
│   │   ├── MaterialFactory.php
│   │   ├── SubjectFactory.php
│   │   └── TopicFactory.php
│   ├── migrations/
│   │   ├── *_create_subjects_table.php
│   │   ├── *_create_topics_table.php
│   │   ├── *_create_materials_table.php
│   │   ├── *_create_exercises_table.php
│   │   ├── *_create_exercise_attempts_table.php
│   │   ├── *_create_flashcards_table.php
│   │   ├── *_create_flashcard_reviews_table.php
│   │   ├── *_create_token_usages_table.php
│   │   ├── *_create_audit_logs_table.php
│   │   └── *_create_notifications_table.php
│   └── seeders/
│       ├── AdminUserSeeder.php
│       ├── DemoDataSeeder.php          # Datos de prueba
│       └── RoleSeeder.php
└── tests/
    └── Unit/
        └── Services/
            ├── AI/
            │   ├── AIManagerTest.php
            │   ├── ExerciseGeneratorTest.php
            │   └── OpenAIProviderTest.php
            └── OCR/
                ├── OCRManagerTest.php
                └── TesseractProviderTest.php
```

---

## 🛠️ Tecnologías Utilizadas

### Backend
- **Laravel 11** - Framework PHP
- **Filament v3.3** - Admin Panel
- **MySQL/MariaDB** - Base de datos
- **Redis** - Caching y queues
- **Laravel Sanctum** - Autenticación API
- **Spatie Laravel Permission** - Roles y permisos

### IA y Machine Learning
- **OpenAI API** - GPT-4o-mini
- **Replicate API** - Llama 2
- **Together.ai API** - Llama 3.1
- **Tesseract OCR** - Extracción de texto

### Frontend
- **Livewire 3** - Componentes reactivos
- **Alpine.js** - JavaScript framework
- **Tailwind CSS** - Styling
- **Chart.js** - Gráficos y analytics
- **Heroicons** - Iconografía

### DevOps y Testing
- **PHPUnit** - Testing framework
- **Laravel Pint** - Code styling
- **Laravel Telescope** - Debugging (desarrollo)

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
- Estados de procesamiento

**exercises**
- Ejercicios generados por IA
- 5 tipos diferentes
- 3 niveles de dificultad
- Respuestas correctas y explicaciones

**exercise_attempts**
- Intentos de estudiantes
- Calificación automática
- Tiempo de completado

**flashcards**
- Sistema de tarjetas de estudio
- Algoritmo SM-2 implementado
- Estadísticas de revisión

**flashcard_reviews**
- Historial de revisiones
- Rating y tiempo
- Estados del algoritmo SM-2

**token_usages**
- Tracking de uso de IA
- Límites mensuales
- Costos calculados

**audit_logs**
- Auditoría de sistema
- Compliance y seguridad

**notifications**
- Centro de notificaciones
- Múltiples canales

### Relaciones

```
User (1) ──► (N) Subjects
Subject (1) ──► (N) Topics
Subject (1) ──► (N) Materials
Topic (1) ──► (N) Materials
User (1) ──► (N) Materials
Material (1) ──► (N) Exercises
User (1) ──► (N) Exercises
Subject (1) ──► (N) Exercises
Topic (1) ──► (N) Exercises
Exercise (1) ──► (N) ExerciseAttempts
User (1) ──► (N) ExerciseAttempts
User (1) ──► (N) Flashcards
Subject (1) ──► (N) Flashcards
Topic (1) ──► (N) Flashcards
Flashcard (1) ──► (N) FlashcardReviews
User (1) ──► (N) FlashcardReviews
User (1) ──► (N) TokenUsages
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
- `process_materials` (OCR)

#### Gestión de Ejercicios
- `view_exercises`
- `create_exercises`
- `edit_exercises`
- `delete_exercises`
- `generate_exercises` (con IA)
- `take_exercises` (estudiantes)

#### Gestión de Flashcards
- `view_flashcards`
- `create_flashcards`
- `edit_flashcards`
- `delete_flashcards`
- `review_flashcards`

#### Panel de Administración
- `access_admin_panel`
- `view_analytics`

---

## 📝 Comandos Útiles

```bash
# Crear nuevo usuario admin
php artisan make:filament-user

# Limpiar caché
php artisan cache:clear
php artisan config:clear
php artisan view:clear

# Refrescar base de datos con datos demo
php artisan migrate:fresh --seed
php artisan db:seed --class=DemoDataSeeder

# Ver rutas
php artisan route:list

# Ejecutar tests
php artisan test

# Ejecutar tests específicos
php artisan test --filter=AIManagerTest
php artisan test --filter=OCRManagerTest
php artisan test --filter=ExerciseGeneratorTest

# Procesar queue jobs
php artisan queue:work

# Ver jobs fallidos
php artisan queue:failed

# Reintentar jobs fallidos
php artisan queue:retry all

# Limpiar jobs completados
php artisan queue:flush

# Ver estadísticas de IA
php artisan tinker
>>> App\Models\TokenUsage::sum('tokens_used')
>>> App\Models\TokenUsage::sum('estimated_cost')
```

---

## 🧪 Tests

### Ejecutar Tests

```bash
# Todos los tests
php artisan test

# Tests específicos
php artisan test --filter=AIManagerTest
php artisan test --filter=ExerciseGeneratorTest
php artisan test --filter=OCRManagerTest

# Con coverage (requiere Xdebug)
php artisan test --coverage
```

### Coverage Actual

- **58 tests** en total
- **20 tests passing** ✅
- **33 tests skipped** (requieren API keys)
- **5 tests failing** (issues menores de tipos de excepciones)

Los tests cubren:
- ✅ Sistema multi-proveedor de IA
- ✅ Generación de ejercicios
- ✅ Procesamiento OCR
- ✅ Tracking de tokens
- ✅ Validación de parámetros

---

## 🎯 Roadmap Futuro

### Features Pendientes

- [ ] **API REST completa** - Endpoints para mobile/web
- [ ] **Integración con Google Calendar** - Sincronización de eventos
- [ ] **Sistema de gamificación** - Badges, levels, achievements
- [ ] **Mind maps automáticos** - Visualización de conocimiento
- [ ] **Sistema social** - Comentarios, ratings, compartir
- [ ] **Live classes** - Video conferencia integrada
- [ ] **Modo offline** - PWA con sincronización
- [ ] **Mobile apps** - iOS y Android nativas
- [ ] **Exportación de datos** - PDF, CSV, JSON
- [ ] **Integración con LMS** - Moodle, Canvas, Blackboard

### Mejoras Técnicas

- [ ] Implementar tests E2E con Pest/Dusk
- [ ] Optimizar queries con índices adicionales
- [ ] Implementar caching agresivo con Redis
- [ ] Añadir rate limiting por usuario
- [ ] Implementar CDN para archivos estáticos
- [ ] Añadir health checks y monitoring
- [ ] Implementar CI/CD completo
- [ ] Dockerizar la aplicación

---

## 🤝 Contribución

Este proyecto está en desarrollo activo. Las contribuciones son bienvenidas.

### Cómo Contribuir

1. Fork el repositorio
2. Crea una rama para tu feature (`git checkout -b feature/amazing-feature`)
3. Commit tus cambios (`git commit -m 'Add amazing feature'`)
4. Push a la rama (`git push origin feature/amazing-feature`)
5. Abre un Pull Request

### Guías de Estilo

- Seguir PSR-12 para código PHP
- Usar Laravel Pint para formateo automático
- Escribir tests para nuevas features
- Documentar cambios en el README

---

## 📄 Licencia

Este proyecto está bajo la licencia MIT.

---

## 📧 Contacto

Para preguntas o sugerencias, contacta al equipo de desarrollo.

---

## 🙏 Agradecimientos

- Laravel Framework Team
- Filament PHP Team
- OpenAI, Replicate y Together.ai por las APIs de IA
- Tesseract OCR Team
- Comunidad open source

---

**Desarrollado con ❤️ para mejorar la experiencia educativa**

## 📈 Estado del Proyecto

**Versión**: 1.0.0-alpha
**Estado**: En desarrollo activo
**Última actualización**: Noviembre 2025
**Completado**: ~75%

### Features Completadas

- ✅ Sistema de autenticación y permisos
- ✅ CRUD completo de materias, temas y materiales
- ✅ Sistema multi-proveedor de IA (OpenAI, Replicate, Together.ai)
- ✅ Procesamiento OCR con Tesseract
- ✅ Generación automática de ejercicios con IA
- ✅ Sistema de flashcards con algoritmo SM-2
- ✅ Dashboard de analytics con gráficos
- ✅ Sistema de notificaciones multi-canal
- ✅ Audit logs para compliance
- ✅ Jobs asíncronos para procesamiento pesado
- ✅ 58 tests unitarios
- ✅ Factories y seeders para testing

### En Progreso

- 🔄 API REST completa
- 🔄 Sistema social (comentarios, ratings)
- 🔄 Integración con Google Calendar

### Por Hacer

- ⏳ Mobile apps (iOS/Android)
- ⏳ Sistema de gamificación
- ⏳ Mind maps automáticos
- ⏳ Live classes
