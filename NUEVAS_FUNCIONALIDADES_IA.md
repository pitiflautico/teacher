# 🤖 Nuevas Funcionalidades de IA para Generación de Contenido

## Resumen

Se han implementado nuevas funcionalidades que permiten usar IA para generar automáticamente contenido de estudio desde materiales existentes. Todas las funcionalidades están integradas en el panel de Filament.

---

## ✨ Funcionalidades Implementadas

### 1. Generar Resúmenes con IA

**Ubicación**: `Materials` → Acción "Generate Summary"

**¿Qué hace?**
- Analiza el contenido de un material procesado
- Genera un resumen completo y estructurado
- Crea un nuevo Material tipo "note" con el resumen
- Se asigna automáticamente al mismo Subject/Topic

**Cómo usar**:
1. Ve a **Materials** en el menú
2. Busca un material que esté **procesado** (con texto extraído)
3. Click en los 3 puntos → **Generate Summary**
4. Confirma la acción
5. Recibirás una notificación cuando esté listo
6. El resumen aparecerá como un nuevo material con título "Resumen: [título original]"

**Características**:
- ✅ Resúmenes claros y concisos
- ✅ Puntos clave destacados
- ✅ Formato markdown para fácil lectura
- ✅ Asignación automática a Subject/Topic
- ✅ Procesamiento asíncrono (no bloquea la UI)

---

### 2. Generar Apuntes Estructurados con IA

**Ubicación**: `Materials` → Acción "Generate Notes"

**¿Qué hace?**
- Analiza el contenido de un material procesado
- Genera apuntes completos y bien organizados
- Incluye títulos, subtítulos, definiciones y ejemplos
- Crea un nuevo Material tipo "note" con los apuntes

**Cómo usar**:
1. Ve a **Materials** en el menú
2. Busca un material que esté **procesado** (con texto extraído)
3. Click en los 3 puntos → **Generate Notes**
4. Confirma la acción
5. Recibirás una notificación cuando esté listo
6. Los apuntes aparecerán como un nuevo material con título "Apuntes: [título original]"

**Características**:
- ✅ Apuntes organizados con títulos y subtítulos
- ✅ Definiciones y conceptos clave
- ✅ Ejemplos incluidos
- ✅ Formato markdown con viñetas y listas
- ✅ Soporte para fórmulas LaTeX
- ✅ Útiles para estudio y preparación de exámenes

---

### 3. Generar Flashcards (Memory Cards) con IA

**Ubicación**: `Materials` → Acción "Generate Flashcards"

**¿Qué hace?**
- Analiza el contenido de un material procesado
- Genera flashcards de estudio (pregunta/respuesta)
- Crea múltiples Flashcards en la base de datos
- Usa algoritmo SM-2 para spaced repetition

**Cómo usar**:
1. Ve a **Materials** en el menú
2. Busca un material que esté **procesado** (con texto extraído)
3. Click en los 3 puntos → **Generate Flashcards**
4. Especifica cuántas flashcards quieres (5-30)
5. Click en "Submit"
6. Recibirás una notificación cuando estén listas
7. Ve a **Flashcards** para verlas y estudiarlas

**Características**:
- ✅ Cantidad configurable (5-30 flashcards)
- ✅ Pregunta/respuesta automáticas
- ✅ Hints opcionales
- ✅ Asignación automática a Subject/Topic
- ✅ Sistema SM-2 para repetición espaciada
- ✅ Listas para usar inmediatamente

---

### 4. Búsqueda Web de Contenido Educativo

**Ubicación**: `Web Search` en el menú principal

**¿Qué hace?**
- Busca contenido educativo en internet
- Usa DuckDuckGo API (sin necesidad de API key)
- Permite guardar resultados como Materials
- Pre-asigna a Subject/Topic si lo deseas

**Cómo usar**:
1. Ve a **Web Search** en el menú
2. Ingresa tu consulta (ej: "Introduction to Calculus")
3. (Opcional) Selecciona Subject y Topic
4. Click en "Search"
5. Revisa los resultados
6. Opciones:
   - **Save**: Guarda un resultado específico
   - **Save All Results**: Guarda todos los resultados
   - **Clear**: Limpia la búsqueda

**Características**:
- ✅ Búsqueda rápida de recursos educativos
- ✅ Pre-asignación a Subject/Topic
- ✅ Guarda enlaces como Materials
- ✅ Tracking de fuente (metadata)
- ✅ Múltiples resultados en una búsqueda

---

## 🔄 Flujo de Trabajo Recomendado

### Flujo Completo: De Búsqueda Web a Material de Estudio

```
1. BUSCAR CONTENIDO
   └─> Web Search → Busca "Introducción a Cálculo"
       └─> Guarda 5 enlaces relevantes como Materials

2. PROCESAR CONTENIDO (si es necesario)
   └─> Materials → Process with OCR (para PDFs/imágenes)
       └─> Extrae texto del material

3. GENERAR CONTENIDO CON IA
   └─> Desde el material procesado:
       ├─> Generate Summary → Resumen conciso
       ├─> Generate Notes → Apuntes estructurados
       └─> Generate Flashcards → 10-20 tarjetas de estudio

4. ESTUDIAR
   └─> Flashcards → Estudia con spaced repetition
   └─> Materials → Lee resúmenes y apuntes
   └─> Exercises → Practica con ejercicios generados
```

### Ejemplo Práctico

**Escenario**: Estás estudiando "Ecuaciones Diferenciales"

1. **Buscar recursos**:
   ```
   Web Search → "ecuaciones diferenciales introducción"
   → Guarda 3-4 enlaces interesantes
   ```

2. **Subir material propio**:
   ```
   Materials → Create → Sube PDF de tu clase
   → Process with OCR → Extrae texto
   ```

3. **Generar contenido de estudio**:
   ```
   Material procesado → Generate Summary
   Material procesado → Generate Notes
   Material procesado → Generate Flashcards (15 cards)
   ```

4. **Resultado**:
   - ✅ 1 Resumen conciso
   - ✅ 1 Apuntes estructurados
   - ✅ 15 Flashcards para estudiar
   - ✅ Todo asignado a "Matemáticas" → "Ecuaciones Diferenciales"

---

## 📊 Organización del Contenido

### Asignación Automática

Todo el contenido generado mantiene la estructura organizativa:

```
Subject: Matemáticas
└─> Topic: Cálculo
    ├─> Material Original (PDF/Link)
    ├─> Material: Resumen (generado)
    ├─> Material: Apuntes (generado)
    └─> Flashcards (10 generadas)
        └─> Todas asignadas al mismo Topic
```

### Ventajas

- ✅ **No se acumula contenido sin organizar**
- ✅ **Todo está categorizado por Subject/Topic**
- ✅ **Fácil de encontrar y revisar**
- ✅ **Metadata indica origen (IA generado)**

---

## 🛠️ Detalles Técnicos

### Jobs Implementados

1. **GenerateSummaryFromMaterial**
   - Input: Material procesado
   - Output: Nuevo Material con resumen
   - Tokens: ~2000 (GPT-4o-mini)
   - Tiempo: ~5-10 segundos

2. **GenerateNotesFromMaterial**
   - Input: Material procesado
   - Output: Nuevo Material con apuntes
   - Tokens: ~3000 (GPT-4o-mini)
   - Tiempo: ~10-15 segundos

3. **GenerateFlashcardsFromMaterial**
   - Input: Material procesado + cantidad
   - Output: N Flashcards en BD
   - Tokens: ~2000 (GPT-4o-mini)
   - Tiempo: ~10-20 segundos

### Costos Estimados (GPT-4o-mini)

- **Resumen**: ~$0.0003 USD
- **Apuntes**: ~$0.0005 USD
- **Flashcards (10)**: ~$0.0003 USD

**Total por material**: ~$0.0011 USD (menos de 1 centavo)

### Requisitos

- ✅ Material debe estar **procesado** (is_processed = true)
- ✅ Material debe tener **extracted_text** no vacío
- ✅ API key de OpenAI configurada en `.env`
- ✅ Queue worker corriendo (`php artisan queue:work`)

---

## 🚀 Beneficios

### Para Estudiantes

1. **Ahorro de tiempo**:
   - No necesitas resumir manualmente
   - No necesitas crear flashcards a mano
   - No necesitas buscar recursos en múltiples sitios

2. **Mejor organización**:
   - Todo está categorizado
   - Fácil de encontrar
   - Sin acumulación de material desordenado

3. **Aprendizaje efectivo**:
   - Resúmenes concisos
   - Apuntes estructurados
   - Flashcards para memorización
   - Spaced repetition automático

### Para Profesores

1. **Generar material rápidamente**:
   - Crear resúmenes de lecturas
   - Generar apuntes de clases
   - Crear flashcards para estudiantes

2. **Compartir recursos**:
   - Buscar contenido educativo
   - Curar materiales de calidad
   - Organizar por temas

---

## 📝 Notas Importantes

### Privacidad y Uso de IA

- Los materiales procesados se envían a la API de IA (OpenAI)
- El contenido NO se almacena en servidores de OpenAI (según política)
- Se recomienda no procesar información sensible

### Calidad del Contenido

- La calidad depende del contenido original
- Mejores resultados con textos claros y estructurados
- Revisa siempre el contenido generado

### Límites

- Límite mensual de tokens (configurable en `.env`)
- Máximo 30 flashcards por generación
- Máximo 20 ejercicios por generación

---

## 🔧 Configuración (para administradores)

### Variables de entorno necesarias

```env
# OpenAI API (requerido)
OPENAI_API_KEY=sk-...
OPENAI_MODEL=gpt-4o-mini
OPENAI_MONTHLY_LIMIT=1000000

# AI Manager
AI_DEFAULT_PROVIDER=openai
AI_TRACK_USAGE=true

# Queue (recomendado)
QUEUE_CONNECTION=database
```

### Iniciar Queue Worker

```bash
# En producción
php artisan queue:work --daemon

# En desarrollo
php artisan queue:work
```

---

## 📚 Archivos Creados/Modificados

### Nuevos Jobs

- `app/Jobs/GenerateSummaryFromMaterial.php`
- `app/Jobs/GenerateNotesFromMaterial.php`
- `app/Jobs/GenerateFlashcardsFromMaterial.php`

### Modificados

- `app/Filament/Resources/MaterialResource.php`
  - Agregadas 3 acciones nuevas
  - Imports de nuevos Jobs

### Nueva Página

- `app/Filament/Pages/WebSearch.php`
- `resources/views/filament/pages/web-search.blade.php`

### Servicios Existentes (usados)

- `app/Services/AI/AIManager.php`
- `app/Services/Web/WebSearchService.php`

---

## 🎯 Próximos Pasos

### Mejoras Futuras Posibles

1. **Editor de contenido generado**:
   - Permitir editar resúmenes antes de guardar
   - Ajustar longitud de resúmenes

2. **Generación masiva**:
   - Procesar múltiples materiales a la vez
   - Generar flashcards desde múltiples fuentes

3. **Personalización de prompts**:
   - Permitir al usuario ajustar el estilo
   - Templates de prompts personalizables

4. **Exportación**:
   - Exportar resúmenes a PDF
   - Exportar flashcards a Anki

---

**Última actualización**: Noviembre 2025
**Versión**: 1.0.0
**Estado**: ✅ Implementado y funcional
