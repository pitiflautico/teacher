# 🚀 Guía de Lanzamiento - Teacher Platform

## 📦 Prerrequisitos

Antes de lanzar, asegúrate de tener instalado:
- PHP 8.2+
- MariaDB/MySQL
- Composer
- Node.js y NPM

## 🎯 Lanzamiento Paso a Paso

### 1️⃣ Verificar Base de Datos

```bash
# Iniciar MariaDB (si no está corriendo)
sudo systemctl start mariadb
# O manualmente:
mariadbd --user=mysql --datadir=/var/lib/mysql --socket=/run/mysqld/mysqld.sock &

# Verificar conexión
mysql -u root -e "SHOW DATABASES;"
```

### 2️⃣ Configurar Variables de Entorno

```bash
# Verificar .env
cat .env | grep -E "DB_|APP_"

# Variables críticas:
# DB_DATABASE=teacher_platform
# DB_USERNAME=root
# DB_PASSWORD=
# APP_URL=http://localhost:8000
```

### 3️⃣ Preparar Base de Datos (Primera vez)

```bash
# Crear base de datos (si no existe)
mysql -u root -e "CREATE DATABASE IF NOT EXISTS teacher_platform CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

# Ejecutar migraciones
php artisan migrate

# Crear usuarios demo
php artisan db:seed --class=AdminUserSeeder

# Crear datos de prueba (opcional)
php artisan db:seed --class=DemoDataSeeder
```

### 4️⃣ Limpiar Caché (Recomendado)

```bash
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear
```

### 5️⃣ Compilar Assets (Solo primera vez o después de cambios)

```bash
npm install
npm run build
```

### 6️⃣ Iniciar Servidor Laravel

```bash
# Terminal 1: Servidor web
php artisan serve --host=0.0.0.0 --port=8000

# El servidor estará disponible en:
# http://localhost:8000
```

### 7️⃣ Iniciar Queue Worker (Opcional - Para procesamiento asíncrono)

```bash
# Terminal 2: Worker de colas (para OCR y generación de ejercicios)
php artisan queue:work

# O con supervisión automática:
php artisan queue:listen --timeout=300
```

### 8️⃣ Acceder a la Aplicación

```
URL: http://localhost:8000/admin

Usuario Admin:
  Email: admin@teacher.com
  Password: admin123

Usuario Estudiante:
  Email: estudiante@teacher.com
  Password: estudiante123
```

---

## 🌐 Rutas y Pantallas Disponibles

### 🔐 Autenticación

| Ruta | Método | Descripción |
|------|--------|-------------|
| `/admin/login` | GET | Página de login |
| `/admin/logout` | POST | Cerrar sesión |

### 🏠 Dashboard

| Ruta | Método | Descripción | Permisos |
|------|--------|-------------|----------|
| `/admin` | GET | Dashboard principal con widgets | Todos |

**Widgets mostrados:**
- `StudentStatsOverview` - Estadísticas del estudiante (solo estudiantes)
- `TeacherStatsOverview` - Estadísticas del profesor (solo admin/profesores)
- `ProgressBySubjectChart` - Gráfico de progreso por materia

### 📚 Gestión de Asignaturas (Subjects)

| Ruta | Método | Descripción | Permisos |
|------|--------|-------------|----------|
| `/admin/subjects` | GET | Listado de asignaturas | `view_subjects` |
| `/admin/subjects/create` | GET | Crear nueva asignatura | `create_subjects` |
| `/admin/subjects/{id}/edit` | GET | Editar asignatura | `edit_subjects` |
| `/admin/subjects/{id}` | DELETE | Eliminar asignatura | `delete_subjects` |

**Campos:**
- Name (nombre de la materia)
- Description (descripción)
- Color (código hex: #RRGGBB)
- Icon (nombre del icono)

**Acciones disponibles:**
- Ver registros
- Crear nueva materia
- Editar materia existente
- Eliminar materia (soft delete)
- Ver temas asociados
- Ver materiales asociados

### 📑 Gestión de Temas (Topics)

| Ruta | Método | Descripción | Permisos |
|------|--------|-------------|----------|
| `/admin/topics` | GET | Listado de temas | `view_topics` |
| `/admin/topics/create` | GET | Crear nuevo tema | `create_topics` |
| `/admin/topics/{id}/edit` | GET | Editar tema | `edit_topics` |
| `/admin/topics/{id}` | DELETE | Eliminar tema | `delete_topics` |

**Campos:**
- Subject (asignatura padre)
- Name (nombre del tema)
- Description (descripción)
- Order (orden de visualización)
- Is Completed (marcado como completado)

**Acciones disponibles:**
- Ver registros
- Crear nuevo tema
- Editar tema existente
- Eliminar tema (soft delete)
- Marcar como completado/pendiente
- Ver materiales asociados

### 📄 Gestión de Materiales (Materials)

| Ruta | Método | Descripción | Permisos |
|------|--------|-------------|----------|
| `/admin/materials` | GET | Listado de materiales | `view_materials` |
| `/admin/materials/create` | GET | Crear nuevo material | `create_materials` |
| `/admin/materials/{id}/edit` | GET | Editar material | `edit_materials` |
| `/admin/materials/{id}` | DELETE | Eliminar material | `delete_materials` |

**Campos:**
- Subject (asignatura)
- Topic (tema)
- Title (título)
- Description (descripción)
- Type (document, image, pdf, link, note)
- File (archivo a subir - opcional)
- Extracted Text (texto extraído por OCR - auto)
- AI Metadata (metadata generada - auto)
- Is Processed (estado de procesamiento - auto)

**Acciones disponibles:**
- Ver registros
- Crear nuevo material
- Editar material existente
- Eliminar material (soft delete)
- **Procesar con OCR** (automático al subir archivo)
- **Generar ejercicios** (botón de acción)
- Ver ejercicios asociados
- Ver texto extraído

**Filtros disponibles:**
- Por asignatura
- Por tema
- Por tipo de material
- Por estado de procesamiento (procesado/sin procesar)

### ✏️ Gestión de Ejercicios (Exercises)

| Ruta | Método | Descripción | Permisos |
|------|--------|-------------|----------|
| `/admin/exercises` | GET | Listado de ejercicios | `view_exercises` |
| `/admin/exercises/create` | GET | Crear nuevo ejercicio | `create_exercises` |
| `/admin/exercises/{id}/edit` | GET | Editar ejercicio | `edit_exercises` |
| `/admin/exercises/{id}` | DELETE | Eliminar ejercicio | `delete_exercises` |

**Campos:**
- Material (material origen)
- Subject (asignatura)
- Topic (tema)
- Title (título)
- Type (multiple_choice, true_false, short_answer, essay, problem_solving)
- Difficulty (easy, medium, hard)
- Question (pregunta)
- Options (opciones - JSON array)
- Correct Answers (respuestas correctas - JSON array)
- Explanation (explicación de la respuesta)
- Hints (pistas - opcional)
- Points (puntos)
- Time Limit (tiempo límite en minutos - opcional)
- Contains Math (contiene matemáticas - boolean)
- Is Active (activo/inactivo)

**Acciones disponibles:**
- Ver registros
- Crear nuevo ejercicio manualmente
- Editar ejercicio existente
- Eliminar ejercicio (soft delete)
- **Generar con IA** (desde material)
- Ver intentos de estudiantes
- Activar/desactivar ejercicio

**Filtros disponibles:**
- Por asignatura
- Por tema
- Por tipo de ejercicio
- Por dificultad
- Por estado (activo/inactivo)
- Con/sin matemáticas

### 📊 Intentos de Ejercicios (Exercise Attempts)

| Ruta | Método | Descripción | Permisos |
|------|--------|-------------|----------|
| `/admin/exercise-attempts` | GET | Listado de intentos | `view_exercise_attempts` |
| `/admin/exercise-attempts/{id}` | GET | Ver detalle de intento | `view_exercise_attempts` |

**Información mostrada:**
- Usuario (estudiante)
- Ejercicio
- Respuesta del estudiante
- Calificación obtenida
- Tiempo tomado
- Fecha de intento
- Feedback automático

**Solo lectura** - Los estudiantes crean intentos desde la página "Take Exercise"

### 🃏 Gestión de Flashcards

| Ruta | Método | Descripción | Permisos |
|------|--------|-------------|----------|
| `/admin/flashcards` | GET | Listado de flashcards | `view_flashcards` |
| `/admin/flashcards/create` | GET | Crear nueva flashcard | `create_flashcards` |
| `/admin/flashcards/{id}/edit` | GET | Editar flashcard | `edit_flashcards` |
| `/admin/flashcards/{id}` | DELETE | Eliminar flashcard | `delete_flashcards` |

**Campos:**
- Subject (asignatura - opcional)
- Topic (tema - opcional)
- Material (material origen - opcional)
- Front (pregunta/término)
- Back (respuesta/definición)
- Hint (pista - opcional)
- Notes (notas personales - opcional)
- Is Active (activa/inactiva)

**Campos automáticos (Algoritmo SM-2):**
- Easiness Factor (factor de facilidad: 1.3-2.5)
- Interval (intervalo de repetición en días)
- Repetitions (número de repeticiones exitosas)
- Next Review At (próxima fecha de revisión)
- Last Reviewed At (última revisión)
- Total Reviews (total de revisiones)
- Correct Reviews (revisiones correctas)
- Streak (racha actual)

**Acciones disponibles:**
- Ver registros
- Crear nueva flashcard
- Editar flashcard existente
- Eliminar flashcard (soft delete)
- **Review** (revisar y calificar 0-5)
- **Reset** (reiniciar algoritmo SM-2)
- Ver historial de revisiones

**Filtros disponibles:**
- Por asignatura
- Por tema
- **Due for Review** (listas para revisar)
- Por estado (activa/inactiva)

**Columnas mostradas:**
- Front (pregunta)
- Accuracy (% de precisión)
- Streak (racha actual)
- Next Review (próxima revisión)
- Total Reviews (total de revisiones)

### 📝 Página de Ejercicios para Estudiantes

| Ruta | Método | Descripción | Permisos |
|------|--------|-------------|----------|
| `/admin/take-exercise` | GET | Tomar ejercicios | Estudiantes |

**Funcionalidad:**
- Seleccionar asignatura
- Seleccionar tema
- Ver ejercicios disponibles
- Responder ejercicios
- Recibir calificación automática
- Ver explicación de respuestas
- Tracking de tiempo
- Guardado automático de intentos

### 💰 Uso de Tokens de IA

| Ruta | Método | Descripción | Permisos |
|------|--------|-------------|----------|
| `/admin/token-usages` | GET | Listado de uso de tokens | Admin |
| `/admin/token-usages/{id}` | GET | Ver detalle de uso | Admin |

**Información mostrada:**
- Usuario
- Proveedor de IA (OpenAI, Replicate, Together.ai)
- Operación (generate_exercise, process_ocr, etc.)
- Tokens usados
- Costo estimado
- Modelo utilizado
- Fecha

**Solo lectura** - Se registra automáticamente al usar servicios de IA

---

## 🌳 Árbol de Flujo Completo del Proyecto

```
┌─────────────────────────────────────────────────────────────────┐
│                    TEACHER PLATFORM                              │
│                 http://localhost:8000/admin                      │
└─────────────────────────────────────────────────────────────────┘
                              │
                              ▼
                    ┌─────────────────┐
                    │   LOGIN PAGE    │
                    │  /admin/login   │
                    └─────────────────┘
                              │
                    ┌─────────┴─────────┐
                    │                   │
              ┌─────▼─────┐      ┌─────▼─────┐
              │   ADMIN   │      │ ESTUDIANTE │
              │   ROLE    │      │    ROLE    │
              └─────┬─────┘      └─────┬──────┘
                    │                   │
                    ▼                   ▼
        ╔═══════════════════╗   ╔═══════════════════╗
        ║  ADMIN DASHBOARD  ║   ║ STUDENT DASHBOARD ║
        ║       /admin      ║   ║      /admin       ║
        ╚═══════════════════╝   ╚═══════════════════╝
                    │                   │
        ┌───────────┼───────────┐      │
        │           │           │      │
        ▼           ▼           ▼      ▼
┌──────────┐ ┌──────────┐ ┌──────────┐ ┌──────────┐
│ WIDGETS  │ │  CRUD    │ │   IA     │ │ EJERC.   │
│          │ │ COMPLETO │ │ SERVICES │ │ STUDENT  │
└──────────┘ └──────────┘ └──────────┘ └──────────┘


═══════════════════════════════════════════════════════════════
                      📊 WIDGETS (Dashboard)
═══════════════════════════════════════════════════════════════

┌─────────────────────────────────────────────────────────────┐
│  👨‍🎓 StudentStatsOverview (solo estudiantes)                  │
│  ├─ Ejercicios completados (total)                          │
│  ├─ Promedio de calificaciones                              │
│  ├─ Tiempo total de estudio                                 │
│  └─ Racha actual                                             │
└─────────────────────────────────────────────────────────────┘

┌─────────────────────────────────────────────────────────────┐
│  👨‍🏫 TeacherStatsOverview (admin/profesores)                  │
│  ├─ Materiales subidos                                      │
│  ├─ Ejercicios generados                                    │
│  ├─ Estudiantes activos                                     │
│  └─ Tasa de procesamiento OCR                               │
└─────────────────────────────────────────────────────────────┘

┌─────────────────────────────────────────────────────────────┐
│  📈 ProgressBySubjectChart (todos)                           │
│  └─ Gráfico de barras: Progreso últimos 7 días por materia │
└─────────────────────────────────────────────────────────────┘


═══════════════════════════════════════════════════════════════
              📚 FLUJO DE GESTIÓN DE CONTENIDO
═══════════════════════════════════════════════════════════════

1️⃣ CREAR ESTRUCTURA DE CONOCIMIENTO
────────────────────────────────────

        /admin/subjects/create
                │
                ▼
        ┌───────────────┐
        │   SUBJECTS    │ ◄─── Color, Icon, Name, Description
        │  (Asignaturas)│
        └───────┬───────┘
                │
                │ has many
                ▼
        /admin/topics/create
                │
                ▼
        ┌───────────────┐
        │    TOPICS     │ ◄─── Name, Description, Order
        │    (Temas)    │
        └───────┬───────┘
                │
                │ has many
                ▼
        /admin/materials/create
                │
                ▼
        ┌───────────────┐
        │   MATERIALS   │ ◄─── Title, Type, File, Description
        │  (Materiales) │
        └───────┬───────┘
                │
                │ (automático al subir archivo)
                ▼


2️⃣ PROCESAMIENTO AUTOMÁTICO CON IA
──────────────────────────────────

    Material Subido (PDF/Image)
            │
            ▼
    ┌───────────────────┐
    │  Job: Process     │
    │  MaterialWithOCR  │
    └─────────┬─────────┘
              │
              ▼
    ┌───────────────────┐
    │  OCR Manager      │
    │  (Tesseract)      │
    └─────────┬─────────┘
              │
              ▼ extracted_text
    ┌───────────────────┐
    │  Material Updated │
    │  is_processed=true│
    └─────────┬─────────┘
              │
              ▼
    ┌───────────────────┐
    │   Notification:   │
    │ Material Processed│
    └───────────────────┘


3️⃣ GENERACIÓN DE EJERCICIOS CON IA
───────────────────────────────────

    /admin/materials/{id}/edit
            │
            │ (botón "Generate Exercises")
            ▼
    ┌───────────────────┐
    │  Formulario Modal │
    │  ├─ Type          │ ◄─── multiple_choice, true_false, etc.
    │  ├─ Difficulty    │ ◄─── easy, medium, hard
    │  └─ Count         │ ◄─── 1-20 ejercicios
    └─────────┬─────────┘
              │
              ▼
    ┌───────────────────┐
    │  Job: Generate    │
    │    Exercises      │
    └─────────┬─────────┘
              │
              ▼
    ┌───────────────────┐
    │   AI Manager      │
    │  (OpenAI/etc.)    │
    └─────────┬─────────┘
              │
              ▼ ejercicios JSON
    ┌───────────────────┐
    │  Exercises Table  │
    │  (5 ejercicios)   │
    └─────────┬─────────┘
              │
              ▼
    ┌───────────────────┐
    │   Notification:   │
    │Exercises Generated│
    └───────────────────┘
              │
              ▼
    /admin/exercises
    (ver ejercicios generados)


═══════════════════════════════════════════════════════════════
            🎓 FLUJO DEL ESTUDIANTE (Learning Path)
═══════════════════════════════════════════════════════════════

    /admin (Dashboard Estudiante)
            │
    ┌───────┴───────┬───────────┬───────────┐
    │               │           │           │
    ▼               ▼           ▼           ▼

┌─────────────┐ ┌──────────┐ ┌─────────┐ ┌──────────┐
│   TOMAR     │ │  CREAR   │ │  VER    │ │  VER     │
│ EJERCICIOS  │ │FLASHCARDS│ │PROGRESO │ │MATERIALES│
└──────┬──────┘ └────┬─────┘ └────┬────┘ └────┬─────┘
       │             │            │           │
       │             │            │           │


4️⃣ TOMAR EJERCICIOS
───────────────────

    /admin/take-exercise
            │
            ▼
    ┌─────────────────────┐
    │ Seleccionar Materia │
    └──────────┬──────────┘
               │
               ▼
    ┌─────────────────────┐
    │  Seleccionar Tema   │
    └──────────┬──────────┘
               │
               ▼
    ┌─────────────────────┐
    │ Lista de Ejercicios │
    │ disponibles         │
    └──────────┬──────────┘
               │
               ▼
    ┌─────────────────────┐
    │  Responder          │
    │  Ejercicio          │
    └──────────┬──────────┘
               │
               ▼
    ┌─────────────────────┐
    │ Calificación        │
    │ Automática          │
    └──────────┬──────────┘
               │
               ▼
    ┌─────────────────────┐
    │ ExerciseAttempt     │
    │ guardado            │
    └──────────┬──────────┘
               │
               ▼
    ┌─────────────────────┐
    │ Ver Explicación     │
    │ + Feedback          │
    └─────────────────────┘


5️⃣ SISTEMA DE FLASHCARDS (Spaced Repetition)
─────────────────────────────────────────────

    /admin/flashcards
            │
            │
    ┌───────┴────────┐
    │                │
    ▼                ▼
CREAR NUEVA    REVISAR EXISTENTES
    │                │
    │                │ (filtro: Due for Review)
    ▼                ▼
┌─────────┐    ┌──────────────┐
│ Flashcard│    │  Flashcards  │
│  Form   │    │ listas hoy   │
│         │    └──────┬───────┘
│ Front   │           │
│ Back    │           │ (abrir flashcard)
│ Hint    │           ▼
│ Subject │    ┌──────────────┐
│ Topic   │    │   Mostrar    │
└────┬────┘    │    Front     │
     │         └──────┬───────┘
     │                │
     │                │ (pensar)
     │                ▼
     │         ┌──────────────┐
     │         │   Mostrar    │
     │         │    Back      │
     │         └──────┬───────┘
     │                │
     │                │ (calificar)
     │                ▼
     │         ┌──────────────┐
     │         │ Rating 0-5   │
     │         │ ├─ 0: Total  │
     │         │ ├─ 1: Difícil│
     │         │ ├─ 2: Difícil│
     │         │ ├─ 3: OK     │
     │         │ ├─ 4: Bien   │
     │         │ └─ 5: Perfecto│
     │         └──────┬───────┘
     │                │
     │                ▼
     │         ┌──────────────┐
     │         │ Algoritmo SM-2│
     │         │ actualiza:   │
     │         │ • easiness   │
     │         │ • interval   │
     │         │ • next_review│
     │         └──────┬───────┘
     │                │
     │                ▼
     │         ┌──────────────┐
     │         │FlashcardReview│
     │         │   guardado   │
     │         └──────────────┘
     │
     ▼
┌────────────┐
│ Auto-set:  │
│ • user_id  │
│ • EF: 2.5  │
│ • interval:0│
│ • next:now │
└────────────┘


═══════════════════════════════════════════════════════════════
                  🔔 SISTEMA DE NOTIFICACIONES
═══════════════════════════════════════════════════════════════

    Centro de Notificaciones
    (icono campana arriba derecha)
            │
            ▼
    ┌─────────────────────┐
    │   Notificaciones    │
    │   Recibidas:        │
    │                     │
    │ 📄 Material         │
    │    Procesado        │
    │                     │
    │ ✏️  Ejercicios      │
    │    Generados        │
    │                     │
    │ ⚠️  Tokens Bajos    │
    │                     │
    └─────────────────────┘

Canales: Database + Email


═══════════════════════════════════════════════════════════════
                   🤖 SERVICIOS DE IA BACKEND
═══════════════════════════════════════════════════════════════

┌─────────────────────────────────────────────────────────────┐
│                       AI MANAGER                             │
│                   (app/Services/AI/)                         │
└─────────────────────────────────────────────────────────────┘
                              │
                    ┌─────────┴─────────┐
                    │                   │
          ┌─────────▼─────────┐  ┌─────▼─────────┐
          │   Provider        │  │   Exercise    │
          │   Management      │  │   Generator   │
          └─────────┬─────────┘  └───────┬───────┘
                    │                    │
        ┌───────────┼────────────┐       │
        │           │            │       │
    ┌───▼───┐  ┌───▼───┐  ┌────▼───┐   │
    │OpenAI │  │Replicate│ │Together│   │
    │  API  │  │  API   │  │.ai API │   │
    └───┬───┘  └───┬────┘  └────┬───┘   │
        │          │            │       │
        └──────────┴────────────┴───────┘
                    │
                    ▼
          ┌─────────────────┐
          │  Token Usage    │
          │   Tracking      │
          └─────────────────┘

┌─────────────────────────────────────────────────────────────┐
│                       OCR MANAGER                            │
│                   (app/Services/OCR/)                        │
└─────────────────────────────────────────────────────────────┘
                              │
                    ┌─────────▼─────────┐
                    │  Tesseract OCR    │
                    │   Provider        │
                    └─────────┬─────────┘
                              │
                    ┌─────────┴─────────┐
                    │                   │
            ┌───────▼───────┐   ┌───────▼───────┐
            │  Extract Text │   │  Multi-lang   │
            │   from Image  │   │   Support     │
            └───────────────┘   └───────────────┘


═══════════════════════════════════════════════════════════════
                  💾 MODELOS Y RELACIONES
═══════════════════════════════════════════════════════════════

User (1) ──has many──► (N) Subjects
                          │
                          │ has many
                          ▼
                       Topics (N)
                          │
                          │ has many
                          ▼
                      Materials (N)
                          │
                          │ has many
                          ▼
                      Exercises (N)
                          │
                          │ has many
                          ▼
                   ExerciseAttempts (N)


User (1) ──has many──► (N) Flashcards
                          │
                          │ has many
                          ▼
                    FlashcardReviews (N)


User (1) ──has many──► (N) TokenUsages

User (1) ──triggers──► (N) AuditLogs


═══════════════════════════════════════════════════════════════
                    🔄 JOBS ASÍNCRONOS (Queues)
═══════════════════════════════════════════════════════════════

Queue: default

┌─────────────────────────┐
│ ProcessMaterialWithOCR  │ ◄─── Triggered: Al subir archivo
│                         │
│ 1. Recibe material ID   │
│ 2. Extrae texto con OCR │
│ 3. Limpia y normaliza   │
│ 4. Guarda en DB         │
│ 5. Envía notificación   │
└─────────────────────────┘

┌─────────────────────────┐
│   GenerateExercises     │ ◄─── Triggered: Botón "Generate"
│                         │
│ 1. Recibe material ID   │
│ 2. Llama AI Manager     │
│ 3. Parsea respuesta JSON│
│ 4. Crea ejercicios      │
│ 5. Envía notificación   │
└─────────────────────────┘


═══════════════════════════════════════════════════════════════
                   🔐 SISTEMA DE PERMISOS
═══════════════════════════════════════════════════════════════

Roles:
├── Admin
│   ├── access_admin_panel ✓
│   ├── view_*  ✓ (todos los recursos)
│   ├── create_* ✓
│   ├── edit_* ✓
│   ├── delete_* ✓
│   ├── generate_exercises ✓
│   ├── process_materials ✓
│   └── view_analytics ✓
│
└── Estudiante
    ├── access_admin_panel ✓
    ├── view_own_subjects ✓
    ├── create_own_content ✓
    ├── edit_own_content ✓
    ├── take_exercises ✓
    ├── create_flashcards ✓
    └── review_flashcards ✓


═══════════════════════════════════════════════════════════════
                    📱 NAVEGACIÓN COMPLETA
═══════════════════════════════════════════════════════════════

Menú Lateral (Sidebar):
├── 🏠 Dashboard
├── 📚 Subjects (Asignaturas)
├── 📑 Topics (Temas)
├── 📄 Materials (Materiales)
├── ✏️  Exercises (Ejercicios)
├── 📊 Exercise Attempts (Intentos)
├── 🃏 Flashcards (Tarjetas)
├── 📝 Take Exercise (Estudiantes)
└── 💰 Token Usages (Admin)

Menú Superior:
├── 🔔 Notifications (campana)
├── 👤 User Menu
│   ├── Profile
│   └── Logout
└── 🌙 Dark Mode Toggle


═══════════════════════════════════════════════════════════════
                 🎯 FLUJO COMPLETO DE USO
═══════════════════════════════════════════════════════════════

┌─────────────────────────────────────────────────────────────┐
│                    FLUJO TÍPICO DE USO                       │
└─────────────────────────────────────────────────────────────┘

PROFESOR/ADMIN:
1. Login → Dashboard
2. Crear Asignatura (Matemáticas)
3. Crear Topics (Álgebra, Cálculo)
4. Subir Material (PDF con ejercicios)
   ├─ Sistema procesa con OCR automáticamente
   └─ Recibe notificación cuando termina
5. Generar Ejercicios con IA
   ├─ Selecciona tipo y dificultad
   ├─ IA genera 5-10 ejercicios
   └─ Recibe notificación cuando termina
6. Crear Flashcards manualmente
7. Ver Analytics en dashboard

ESTUDIANTE:
1. Login → Dashboard
2. Ver Materiales disponibles
3. Ir a "Take Exercise"
   ├─ Seleccionar materia/tema
   ├─ Responder ejercicios
   └─ Ver calificación + explicación
4. Ir a "Flashcards"
   ├─ Filtrar "Due for Review"
   ├─ Revisar tarjetas
   └─ Calificar respuestas (0-5)
5. Ver Progreso en dashboard
6. Recibir notificaciones de nuevo contenido

```

---

## 🎯 Comandos Rápidos de Uso Diario

```bash
# Iniciar servidor
php artisan serve

# Iniciar queue worker
php artisan queue:work

# Ver logs en tiempo real
tail -f storage/logs/laravel.log

# Limpiar todo
php artisan optimize:clear

# Refrescar base de datos
php artisan migrate:fresh --seed
php artisan db:seed --class=DemoDataSeeder
```

---

## ⚡ Troubleshooting

### Problema: No carga el login
```bash
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

### Problema: Error de permisos
```bash
chmod -R 775 storage bootstrap/cache
chown -R $USER:www-data storage bootstrap/cache
```

### Problema: Queue jobs no se procesan
```bash
# Asegúrate de que el worker esté corriendo
php artisan queue:work

# Ver jobs fallidos
php artisan queue:failed

# Reintentar
php artisan queue:retry all
```

### Problema: OCR no funciona
```bash
# Verificar Tesseract instalado
which tesseract
tesseract --version

# Instalar si no existe
# Ubuntu/Debian:
sudo apt-get install tesseract-ocr tesseract-ocr-spa tesseract-ocr-eng
```

---

## 📋 Checklist Pre-Lanzamiento

- [ ] MariaDB corriendo
- [ ] Base de datos creada (`teacher_platform`)
- [ ] Migraciones ejecutadas
- [ ] Seeders ejecutados (usuarios demo)
- [ ] .env configurado correctamente
- [ ] Caché limpiado
- [ ] Assets compilados (npm run build)
- [ ] Servidor Laravel iniciado (puerto 8000)
- [ ] Queue worker iniciado (opcional)
- [ ] Tesseract OCR instalado (opcional)

---

¡Todo listo para usar! 🚀
