# 🗺️ ROADMAP - Teacher Platform

## Estado Actual: ~40% Completado

Este documento detalla todas las features pendientes de implementación.

---

## ✅ LO QUE YA ESTÁ FUNCIONANDO (ETAPAS 1-7)

### Core Features Implementadas
- ✅ Laravel 11 + Filament v3.3
- ✅ Autenticación y roles (Spatie Permission)
- ✅ CRUD completo: Subjects, Topics, Materials, Exercises
- ✅ Sistema multi-proveedor de IA (OpenAI, Replicate, Together.ai)
- ✅ OCR automático con Tesseract
- ✅ Generación de ejercicios con IA (5 tipos, 3 niveles)
- ✅ Sistema de Flashcards con algoritmo SM-2
- ✅ Dashboard con analytics (3 widgets)
- ✅ Sistema de notificaciones (Database + Email)
- ✅ Audit logs para compliance
- ✅ Jobs asíncronos (Queue)
- ✅ 58 tests unitarios
- ✅ Servidor funcionando en http://localhost:8000

---

## 🚧 PENDIENTE DE IMPLEMENTACIÓN (60%)

### FASE 8: Mejoras Core (2-3 semanas)

#### 8.1 LaTeX Rendering en Ejercicios
**Status**: Parcial (KaTeX instalado)
**Falta**:
- [ ] Componente Livewire para renderizar LaTeX
- [ ] Integración en ExerciseResource
- [ ] Integración en TakeExercise page
- [ ] Editor con preview en tiempo real
- [ ] Soporte para fórmulas inline y display

**Archivos**:
```
app/Http/Livewire/LatexRenderer.php (crear)
resources/views/livewire/latex-renderer.blade.php (crear)
app/Filament/Resources/ExerciseResource.php (modificar)
```

**Implementación**:
```php
// LatexRenderer component
use Livewire\Component;

class LatexRenderer extends Component
{
    public $content;

    public function render()
    {
        return view('livewire.latex-renderer');
    }
}
```

```html
<!-- latex-renderer.blade.php -->
<div x-data x-init="
    renderMathInElement(document.body, {
        delimiters: [
            {left: '$$', right: '$$', display: true},
            {left: '$', right: '$', display: false}
        ]
    })
">
    {!! $content !!}
</div>
```

#### 8.2 User Profile Completo
**Status**: Migración creada
**Falta**:
- [ ] UserProfileResource en Filament
- [ ] Upload de avatar con Spatie Media Library
- [ ] Página de configuración de preferencias IA
- [ ] Privacy settings (público/privado)
- [ ] Relación User hasOne Profile

**Migración** (ya está lista para ejecutar):
```bash
php artisan migrate
```

**Archivos a crear**:
```
app/Filament/Resources/UserProfileResource.php
app/Filament/Pages/ProfileSettings.php
app/Models/UserProfile.php (configurar)
```

#### 8.3 Material Preview & AI Summary
**Status**: No iniciado
**Falta**:
- [ ] Visor PDF inline (usando PDF.js)
- [ ] Método generateSummary() en Material model
- [ ] Job: GenerateMaterialSummary
- [ ] Botón "Generate Summary" en MaterialResource
- [ ] Mostrar resumen en card

**Implementación**:
```php
// Material.php
public function generateSummary()
{
    $aiManager = app(AIManager::class);
    $prompt = "Summarize this educational content in 3-5 bullet points:\n\n" .
              $this->extracted_text;

    $summary = $aiManager->complete($prompt);
    $this->ai_summary = $summary;
    $this->save();

    return $summary;
}
```

### FASE 9: Sistema de Calendario (2 semanas)

#### 9.1 Calendar CRUD Básico
**Status**: Migración creada
**Falta**:
- [ ] CalendarEventResource en Filament
- [ ] Modelo CalendarEvent con relaciones
- [ ] Validaciones de fechas (start < end)
- [ ] Colores por tipo de evento
- [ ] Recordatorios automáticos

**Ejecutar**:
```bash
php artisan migrate
php artisan make:filament-resource CalendarEvent
```

#### 9.2 Vista de Calendario FullCalendar.js
**Status**: No iniciado
**Falta**:
- [ ] Instalar FullCalendar: `npm install @fullcalendar/core @fullcalendar/daygrid @fullcalendar/timegrid`
- [ ] Página Filament custom: CalendarPage
- [ ] API endpoint para eventos: `/api/calendar/events`
- [ ] Drag & drop para mover eventos
- [ ] Click para crear evento rápido

**Archivos**:
```
app/Filament/Pages/CalendarPage.php
resources/views/filament/pages/calendar-page.blade.php
routes/api.php (agregar endpoint)
```

#### 9.3 Google Calendar Sync
**Status**: No iniciado
**Falta**:
- [ ] Instalar Google API: `composer require google/apiclient`
- [ ] OAuth setup en Google Cloud Console
- [ ] Modelo GoogleCalendarToken
- [ ] Service: GoogleCalendarService
- [ ] Sincronización bidireccional (push/pull)
- [ ] Webhook para actualizaciones en tiempo real

**Variables .env**:
```env
GOOGLE_CLIENT_ID=
GOOGLE_CLIENT_SECRET=
GOOGLE_REDIRECT_URI=http://localhost:8000/auth/google/callback
```

**Implementación**:
```php
// GoogleCalendarService.php
public function syncEvents(User $user)
{
    $client = new Google_Client();
    $client->setAccessToken($user->googleToken->access_token);

    $service = new Google_Service_Calendar($client);
    $events = $service->events->listEvents('primary');

    foreach ($events->getItems() as $event) {
        CalendarEvent::updateOrCreate([
            'google_event_id' => $event->getId(),
        ], [
            'user_id' => $user->id,
            'title' => $event->getSummary(),
            'start_at' => $event->getStart()->getDateTime(),
            'end_at' => $event->getEnd()->getDateTime(),
        ]);
    }
}
```

### FASE 10: Mind Maps Visuales (2-3 semanas)

#### 10.1 Mind Map CRUD
**Status**: Migración creada
**Falta**:
- [ ] MindMapResource en Filament
- [ ] Modelo MindMap con JSON casts
- [ ] Generar thumbnail con puppeteer/chromium
- [ ] Compartir mapas (público/privado)

#### 10.2 Canvas Interactivo D3.js
**Status**: No iniciado
**Falta**:
- [ ] Instalar D3: `npm install d3`
- [ ] Página custom: MindMapEditor
- [ ] Canvas drag & drop
- [ ] Agregar nodos y conexiones
- [ ] Guardar como JSON en DB
- [ ] Zoom y pan
- [ ] Exportar a PNG/SVG

**Componente Vue/Livewire**:
```javascript
// MindMapCanvas.vue
<template>
    <div id="mindmap" ref="canvas"></div>
</template>

<script>
import * as d3 from 'd3';

export default {
    mounted() {
        this.initCanvas();
    },
    methods: {
        initCanvas() {
            const svg = d3.select(this.$refs.canvas)
                .append('svg')
                .attr('width', '100%')
                .attr('height', '600px');

            // Initialize force simulation
            const simulation = d3.forceSimulation()
                .force('link', d3.forceLink())
                .force('charge', d3.forceManyBody())
                .force('center', d3.forceCenter());
        }
    }
}
</script>
```

#### 10.3 AI: Generar Mind Map desde Material
**Status**: No iniciado
**Falta**:
- [ ] Prompt engineering para extraer conceptos
- [ ] Job: GenerateMindMapFromMaterial
- [ ] Parser de respuesta IA a JSON nodes/edges
- [ ] Algoritmo de layout automático

**Prompt**:
```
Analyze this educational content and extract:
1. Main concepts (max 10)
2. Relationships between concepts
3. Hierarchy (parent-child)

Content: {material.extracted_text}

Return as JSON:
{
  "nodes": [
    {"id": 1, "label": "Concept", "level": 0},
    ...
  ],
  "edges": [
    {"from": 1, "to": 2, "label": "relates to"},
    ...
  ]
}
```

#### 10.4 Convertir Mind Map → Flashcards
**Status**: No iniciado
**Falta**:
- [ ] Botón "Generate Flashcards" en MindMapResource
- [ ] Algoritmo: cada nodo = 1 flashcard
- [ ] Front: concepto principal
- [ ] Back: descripción + nodos relacionados
- [ ] Batch creation de flashcards

### FASE 11: Social Features (3-4 semanas)

#### 11.1 User Profiles Públicos
**Status**: Migración creada
**Falta**:
- [ ] Ruta: `/profile/{username}`
- [ ] Página ProfilePage con stats públicas
- [ ] Timeline de actividad
- [ ] Materiales/mapas compartidos
- [ ] Followers/Following count

#### 11.2 Sistema de Follow
**Status**: Migración creada (follows table)
**Falta**:
- [ ] Modelo Follow con relaciones
- [ ] Botón "Follow/Unfollow"
- [ ] Página "Following" y "Followers"
- [ ] Notificación cuando alguien te sigue
- [ ] Feed de actividad de seguidos

**Implementación**:
```php
// User.php
public function following()
{
    return $this->belongsToMany(User::class, 'follows', 'follower_id', 'following_id');
}

public function followers()
{
    return $this->belongsToMany(User::class, 'follows', 'following_id', 'follower_id');
}

public function follow(User $user)
{
    $this->following()->attach($user->id);
    $user->notify(new NewFollowerNotification($this));
}
```

#### 11.3 Grupos Temáticos
**Status**: Migraciones creadas (groups, group_members)
**Falta**:
- [ ] GroupResource en Filament
- [ ] Página de grupo con wall/posts
- [ ] Roles dentro del grupo (admin, moderator, member)
- [ ] Invitaciones a grupo
- [ ] Recursos compartidos en grupo

#### 11.4 Chat en Tiempo Real
**Status**: Migración creada (messages)
**Falta**:
- [ ] Instalar Laravel Echo + Pusher: `composer require pusher/pusher-php-server`
- [ ] MessageResource (solo lectura para admin)
- [ ] Componente Livewire: ChatWidget
- [ ] Broadcasting con WebSockets
- [ ] Notificaciones de mensajes nuevos
- [ ] Chat 1-1 y chat de grupo

**Variables .env**:
```env
BROADCAST_DRIVER=pusher
PUSHER_APP_ID=
PUSHER_APP_KEY=
PUSHER_APP_SECRET=
PUSHER_APP_CLUSTER=mt1
```

**Implementación**:
```php
// Message.php
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class Message extends Model implements ShouldBroadcast
{
    public function broadcastOn()
    {
        return new PrivateChannel('chat.' . $this->recipient_id);
    }
}
```

```javascript
// resources/js/app.js
import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

window.Echo = new Echo({
    broadcaster: 'pusher',
    key: process.env.MIX_PUSHER_APP_KEY,
    cluster: process.env.MIX_PUSHER_APP_CLUSTER,
});

Echo.private(`chat.${userId}`)
    .listen('MessageSent', (e) => {
        console.log(e.message);
    });
```

#### 11.5 Sistema de Ratings y Comentarios
**Status**: No iniciado
**Falta**:
- [ ] Tabla ratings (polymorphic)
- [ ] Tabla comments (polymorphic)
- [ ] Trait Rateable y Commentable
- [ ] Componente de estrellas en Resources
- [ ] Moderación de comentarios

### FASE 12: Reportes Avanzados (1-2 semanas)

#### 12.1 Exportación PDF
**Status**: No iniciado
**Falta**:
- [ ] Instalar DomPDF: `composer require barryvdh/laravel-dompdf`
- [ ] Vista PDF: `resources/views/reports/student-progress.blade.php`
- [ ] Gráficos como imágenes (Chart.js to image)
- [ ] Botón "Export PDF" en dashboard
- [ ] Logo y branding personalizable

**Implementación**:
```php
use Barryvdh\DomPDF\Facade\Pdf;

public function exportPDF()
{
    $user = auth()->user();
    $data = [
        'user' => $user,
        'exercises' => $user->exerciseAttempts()->with('exercise')->get(),
        'flashcards' => $user->flashcards()->get(),
        'progress' => $this->calculateProgress($user),
    ];

    $pdf = Pdf::loadView('reports.student-progress', $data);
    return $pdf->download('my-progress-'.now()->format('Y-m-d').'.pdf');
}
```

#### 12.2 Recomendaciones IA Automáticas
**Status**: No iniciado
**Falta**:
- [ ] Analizar historial de ejercicios fallados
- [ ] Job diario: GenerateRecommendations
- [ ] Sugerir temas débiles para repasar
- [ ] Sugerir materiales relacionados
- [ ] Notificación con recomendaciones

**Prompt IA**:
```
Analyze this student's performance:
- Subjects: {subjects_with_scores}
- Failed exercises: {failed_topics}
- Study time: {study_hours}
- Last activity: {last_active}

Generate 3-5 personalized recommendations to improve.
```

### FASE 13: Gamificación (1-2 semanas)

#### 13.1 Sistema de Puntos
**Status**: No iniciado
**Falta**:
- [ ] Tabla: points (polymorphic)
- [ ] Eventos para otorgar puntos
- [ ] Leaderboard semanal/mensual
- [ ] Puntos por: completar ejercicio, crear flashcard, estudiar diario, racha

#### 13.2 Badges y Achievements
**Status**: No iniciado
**Falta**:
- [ ] Tabla: badges, user_badges
- [ ] Badges predefinidos (ej: "10 días seguidos", "100 ejercicios")
- [ ] Sistema de detección automática
- [ ] Notificación al desbloquear badge
- [ ] Mostrar badges en perfil

**Badges sugeridos**:
- 🔥 Fire Starter: 7 días seguidos
- 📚 Bookworm: 50 materiales leídos
- 🎯 Sharpshooter: 100 ejercicios correctos
- 🧠 Brain Master: 500 flashcards revisadas
- 👥 Social Butterfly: 10 seguidores
- 🏆 Top Student: #1 en leaderboard

#### 13.3 Niveles y Experiencia
**Status**: No iniciado
**Falta**:
- [ ] Campo `experience_points` en users
- [ ] Campo `level` en users
- [ ] Algoritmo de level-up (ej: level = sqrt(xp/100))
- [ ] Barra de progreso de XP
- [ ] Recompensas por level-up

### FASE 14: REST API Completa (2 semanas)

#### 14.1 API Authentication
**Status**: Laravel Sanctum ya está instalado
**Falta**:
- [ ] Rutas en `routes/api.php`
- [ ] API tokens con Sanctum
- [ ] Rate limiting por usuario
- [ ] API documentation con Scribe

#### 14.2 API Endpoints
**Status**: No iniciado

**Endpoints a crear**:
```
POST   /api/auth/register
POST   /api/auth/login
POST   /api/auth/logout
GET    /api/user

GET    /api/subjects
POST   /api/subjects
GET    /api/subjects/{id}
PUT    /api/subjects/{id}
DELETE /api/subjects/{id}

GET    /api/materials
POST   /api/materials (con upload)
GET    /api/materials/{id}
PUT    /api/materials/{id}
DELETE /api/materials/{id}
POST   /api/materials/{id}/generate-summary
POST   /api/materials/{id}/generate-exercises

GET    /api/exercises
GET    /api/exercises/{id}
POST   /api/exercises/{id}/attempt
GET    /api/exercises/attempts

GET    /api/flashcards
POST   /api/flashcards
GET    /api/flashcards/due
POST   /api/flashcards/{id}/review

GET    /api/calendar/events
POST   /api/calendar/events
PUT    /api/calendar/events/{id}
DELETE /api/calendar/events/{id}

GET    /api/dashboard/stats
GET    /api/dashboard/progress
```

**Implementación**:
```php
// app/Http/Controllers/API/SubjectController.php
class SubjectController extends Controller
{
    public function index()
    {
        return SubjectResource::collection(
            Subject::where('user_id', auth()->id())->get()
        );
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'color' => 'required|regex:/^#[0-9A-F]{6}$/i',
        ]);

        $subject = auth()->user()->subjects()->create($validated);

        return new SubjectResource($subject);
    }
}
```

#### 14.3 API Resources (Transformers)
**Status**: No iniciado
**Falta**:
- [ ] SubjectResource
- [ ] MaterialResource
- [ ] ExerciseResource
- [ ] FlashcardResource
- [ ] CalendarEventResource

### FASE 15: Features Avanzadas (3-4 semanas)

#### 15.1 Sesiones Online (Zoom/Google Meet)
**Status**: No iniciado
**Falta**:
- [ ] Integración Zoom SDK o Google Meet API
- [ ] Tabla: online_sessions
- [ ] Agendar sesión en calendario
- [ ] Link de meeting generado automáticamente
- [ ] Recordatorio 15 min antes

#### 15.2 Modo Offline (PWA)
**Status**: No iniciado
**Falta**:
- [ ] Service Worker
- [ ] manifest.json
- [ ] Cache de assets críticos
- [ ] LocalStorage para datos offline
- [ ] Sincronización al reconectar

#### 15.3 Importar desde Google Drive
**Status**: No iniciado
**Falta**:
- [ ] Google Drive API
- [ ] OAuth para Drive
- [ ] Selector de archivos de Drive
- [ ] Descargar y procesar con OCR

#### 15.4 Exportación de Datos (GDPR Compliance)
**Status**: No iniciado
**Falta**:
- [ ] Botón "Download My Data"
- [ ] Job: GenerateUserDataExport
- [ ] ZIP con todos los datos en JSON
- [ ] Eliminar cuenta (soft delete)

---

## 🛠️ COMANDOS ÚTILES PARA CONTINUAR

```bash
# Ejecutar migraciones nuevas
php artisan migrate

# Crear Resources de Filament para nuevos modelos
php artisan make:filament-resource CalendarEvent --generate
php artisan make:filament-resource MindMap --generate
php artisan make:filament-resource Group --generate

# Instalar dependencias pendientes
npm install @fullcalendar/core @fullcalendar/daygrid d3
composer require google/apiclient pusher/pusher-php-server barryvdh/laravel-dompdf

# Iniciar servicios
php artisan serve
php artisan queue:work
npm run dev  # En modo desarrollo para hot reload
```

---

## 📦 ESTRUCTURA DE ARCHIVOS FINAL (Proyectada)

```
teacher/
├── app/
│   ├── Filament/
│   │   ├── Pages/
│   │   │   ├── CalendarPage.php
│   │   │   ├── MindMapEditor.php
│   │   │   ├── ProfileSettings.php
│   │   │   └── TakeExercise.php (✅ ya existe)
│   │   ├── Resources/
│   │   │   ├── CalendarEventResource.php (crear)
│   │   │   ├── ExerciseResource.php (✅)
│   │   │   ├── FlashcardResource.php (✅)
│   │   │   ├── GroupResource.php (crear)
│   │   │   ├── MaterialResource.php (✅)
│   │   │   ├── MindMapResource.php (crear)
│   │   │   ├── SubjectResource.php (✅)
│   │   │   └── UserProfileResource.php (crear)
│   │   └── Widgets/
│   │       ├── LeaderboardWidget.php (crear)
│   │       ├── ProgressBySubjectChart.php (✅)
│   │       ├── RecommendationsWidget.php (crear)
│   │       ├── StudentStatsOverview.php (✅)
│   │       └── TeacherStatsOverview.php (✅)
│   ├── Http/
│   │   ├── Controllers/
│   │   │   └── API/ (crear toda la carpeta)
│   │   │       ├── AuthController.php
│   │   │       ├── CalendarController.php
│   │   │       ├── ExerciseController.php
│   │   │       ├── FlashcardController.php
│   │   │       ├── MaterialController.php
│   │   │       └── SubjectController.php
│   │   └── Livewire/ (crear)
│   │       ├── ChatWidget.php
│   │       ├── LatexRenderer.php
│   │       └── MindMapCanvas.php
│   ├── Jobs/
│   │   ├── GenerateExercises.php (✅)
│   │   ├── GenerateMaterialSummary.php (crear)
│   │   ├── GenerateMindMapFromMaterial.php (crear)
│   │   ├── GenerateRecommendations.php (crear)
│   │   ├── ProcessMaterialWithOCR.php (✅)
│   │   └── SyncGoogleCalendar.php (crear)
│   ├── Models/
│   │   ├── AuditLog.php (✅)
│   │   ├── Badge.php (crear)
│   │   ├── CalendarEvent.php (crear)
│   │   ├── Comment.php (crear)
│   │   ├── Exercise.php (✅)
│   │   ├── ExerciseAttempt.php (✅)
│   │   ├── Flashcard.php (✅)
│   │   ├── FlashcardReview.php (✅)
│   │   ├── Follow.php (crear)
│   │   ├── Group.php (crear)
│   │   ├── GroupMember.php (crear)
│   │   ├── Material.php (✅)
│   │   ├── Message.php (crear)
│   │   ├── MindMap.php (crear)
│   │   ├── Point.php (crear)
│   │   ├── Rating.php (crear)
│   │   ├── Subject.php (✅)
│   │   ├── TokenUsage.php (✅)
│   │   ├── Topic.php (✅)
│   │   ├── User.php (✅)
│   │   └── UserProfile.php (crear)
│   ├── Notifications/
│   │   ├── BadgeUnlockedNotification.php (crear)
│   │   ├── ExercisesGeneratedNotification.php (✅)
│   │   ├── LowTokensWarning.php (✅)
│   │   ├── MaterialProcessedNotification.php (✅)
│   │   ├── NewFollowerNotification.php (crear)
│   │   ├── NewMessageNotification.php (crear)
│   │   └── RecommendationsReadyNotification.php (crear)
│   └── Services/
│       ├── AI/ (✅)
│       ├── Calendar/
│       │   └── GoogleCalendarService.php (crear)
│       ├── Gamification/
│       │   ├── BadgeService.php (crear)
│       │   └── PointsService.php (crear)
│       └── OCR/ (✅)
├── database/
│   ├── factories/ (✅ todos creados)
│   ├── migrations/ (7 nuevas pendientes de configurar)
│   └── seeders/
│       ├── AdminUserSeeder.php (✅)
│       ├── BadgeSeeder.php (crear)
│       └── DemoDataSeeder.php (✅)
├── resources/
│   ├── js/
│   │   ├── app.js (modificar para Echo)
│   │   └── components/
│   │       ├── MindMapCanvas.vue (crear)
│   │       └── ChatWidget.vue (crear)
│   └── views/
│       ├── filament/
│       │   └── pages/
│       │       ├── calendar-page.blade.php (crear)
│       │       └── mind-map-editor.blade.php (crear)
│       ├── livewire/
│       │   ├── chat-widget.blade.php (crear)
│       │   └── latex-renderer.blade.php (crear)
│       └── reports/
│           └── student-progress.blade.php (crear)
├── routes/
│   ├── api.php (agregar todos los endpoints)
│   └── web.php (✅)
└── tests/
    └── Feature/
        └── API/ (crear todos los tests API)
```

---

## 🎯 PRIORIDADES SUGERIDAS

### Corto Plazo (1-2 semanas)
1. **Calendar**: Funcionalidad más solicitada por usuarios
2. **LaTeX**: Crítico para matemáticas/ciencias
3. **User Profiles**: Mejora UX

### Mediano Plazo (3-4 semanas)
4. **Mind Maps**: Feature diferenciadora
5. **Social Features básicos**: Follow, grupos
6. **API REST**: Para mobile apps

### Largo Plazo (2-3 meses)
7. **Chat en tiempo real**
8. **Gamificación completa**
9. **Google Calendar sync**
10. **Sesiones online**

---

## 📝 NOTAS FINALES

Este roadmap es exhaustivo y cubre TODO lo especificado en tu requerimiento original. La implementación completa tomará aproximadamente **3-4 meses** de desarrollo full-time.

**Recomendación**: Implementar por fases completas en lugar de hacer todo parcialmente. Cada fase puede desplegarse a producción incrementalmente.

**Contacto para implementación**: Cada sección incluye código de ejemplo y comandos exactos para facilitar la implementación.
