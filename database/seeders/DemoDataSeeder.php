<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DemoDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get admin and student users
        $admin = \App\Models\User::where('email', 'admin@teacher.com')->first();
        $student = \App\Models\User::where('email', 'estudiante@teacher.com')->first();

        if (!$admin || !$student) {
            $this->command->error('Demo users not found. Run UserSeeder first.');
            return;
        }

        // Create subjects for admin
        $mathSubject = \App\Models\Subject::create([
            'user_id' => $admin->id,
            'name' => 'Matemáticas',
            'description' => 'Curso completo de matemáticas avanzadas',
            'color' => '#3B82F6',
            'icon' => 'calculator',
        ]);

        $physicsSubject = \App\Models\Subject::create([
            'user_id' => $admin->id,
            'name' => 'Física',
            'description' => 'Fundamentos de física clásica y moderna',
            'color' => '#8B5CF6',
            'icon' => 'atom',
        ]);

        // Create topics for math
        $algebraTopic = \App\Models\Topic::create([
            'subject_id' => $mathSubject->id,
            'name' => 'Álgebra Lineal',
            'description' => 'Matrices, determinantes y sistemas de ecuaciones',
            'order' => 1,
        ]);

        $calculusTopic = \App\Models\Topic::create([
            'subject_id' => $mathSubject->id,
            'name' => 'Cálculo Diferencial',
            'description' => 'Límites, derivadas y aplicaciones',
            'order' => 2,
        ]);

        // Create topics for physics
        $mechanicsTopic = \App\Models\Topic::create([
            'subject_id' => $physicsSubject->id,
            'name' => 'Mecánica Clásica',
            'description' => 'Leyes de Newton y cinemática',
            'order' => 1,
        ]);

        // Create materials
        $material1 = \App\Models\Material::create([
            'user_id' => $admin->id,
            'subject_id' => $mathSubject->id,
            'topic_id' => $algebraTopic->id,
            'title' => 'Introducción a Matrices',
            'description' => 'Conceptos básicos de matrices y operaciones fundamentales',
            'type' => 'note',
            'extracted_text' => "Una matriz es un arreglo rectangular de números ordenados en filas y columnas.\n\nOperaciones básicas:\n1. Suma de matrices: A + B\n2. Multiplicación por escalar: k·A\n3. Producto de matrices: A·B\n\nPropiedades:\n- La suma es conmutativa: A + B = B + A\n- El producto NO es conmutativo: A·B ≠ B·A\n- Elemento neutro de la suma: matriz cero",
            'is_processed' => true,
            'processed_at' => now(),
        ]);

        $material2 = \App\Models\Material::create([
            'user_id' => $admin->id,
            'subject_id' => $mathSubject->id,
            'topic_id' => $calculusTopic->id,
            'title' => 'Límites y Continuidad',
            'description' => 'Definición formal de límite y concepto de continuidad',
            'type' => 'note',
            'extracted_text' => "El límite de una función f(x) cuando x tiende a un valor a es L si:\nlim(x→a) f(x) = L\n\nPropiedades de límites:\n1. lim(x→a) [f(x) + g(x)] = lim f(x) + lim g(x)\n2. lim(x→a) [k·f(x)] = k·lim f(x)\n3. lim(x→a) [f(x)·g(x)] = lim f(x) · lim g(x)\n\nUna función es continua en x=a si:\n1. f(a) existe\n2. lim(x→a) f(x) existe\n3. lim(x→a) f(x) = f(a)",
            'is_processed' => true,
            'processed_at' => now(),
        ]);

        // Create exercises for material 1
        $exercise1 = \App\Models\Exercise::create([
            'user_id' => $admin->id,
            'material_id' => $material1->id,
            'subject_id' => $mathSubject->id,
            'topic_id' => $algebraTopic->id,
            'title' => 'Propiedades de Matrices',
            'type' => 'multiple_choice',
            'difficulty' => 'easy',
            'question' => '¿Cuál de las siguientes afirmaciones sobre matrices es VERDADERA?',
            'options' => json_encode([
                'El producto de matrices siempre es conmutativo',
                'La suma de matrices es conmutativa',
                'Una matriz puede multiplicarse con cualquier otra matriz',
                'El elemento neutro de la suma es la matriz identidad',
            ]),
            'correct_answers' => json_encode(['La suma de matrices es conmutativa']),
            'explanation' => 'La suma de matrices sí cumple la propiedad conmutativa: A + B = B + A. En cambio, el producto de matrices NO es conmutativo en general.',
            'points' => 10,
        ]);

        $exercise2 = \App\Models\Exercise::create([
            'user_id' => $admin->id,
            'material_id' => $material1->id,
            'subject_id' => $mathSubject->id,
            'topic_id' => $algebraTopic->id,
            'title' => 'Conmutatividad del Producto de Matrices',
            'type' => 'true_false',
            'difficulty' => 'easy',
            'question' => 'El producto de matrices A·B siempre es igual a B·A',
            'options' => json_encode(['Verdadero', 'Falso']),
            'correct_answers' => json_encode(['Falso']),
            'explanation' => 'El producto de matrices NO es conmutativo. En general, A·B ≠ B·A.',
            'points' => 5,
        ]);

        // Create exercises for material 2
        $exercise3 = \App\Models\Exercise::create([
            'user_id' => $admin->id,
            'material_id' => $material2->id,
            'subject_id' => $mathSubject->id,
            'topic_id' => $calculusTopic->id,
            'title' => 'Continuidad de Funciones',
            'type' => 'short_answer',
            'difficulty' => 'medium',
            'question' => 'Explica qué condiciones debe cumplir una función para ser continua en un punto x=a',
            'options' => null,
            'correct_answers' => json_encode(['Una función es continua en x=a si: 1) f(a) existe, 2) el límite cuando x tiende a "a" existe, y 3) el límite es igual al valor de la función: lim(x→a) f(x) = f(a)']),
            'explanation' => 'La continuidad requiere que la función esté definida en el punto, que exista el límite, y que ambos valores coincidan.',
            'points' => 15,
        ]);

        // Create flashcards for the admin
        $flashcard1 = \App\Models\Flashcard::create([
            'user_id' => $admin->id,
            'subject_id' => $mathSubject->id,
            'topic_id' => $algebraTopic->id,
            'front' => '¿Qué es una matriz?',
            'back' => 'Una matriz es un arreglo rectangular de números ordenados en filas y columnas. Se denota generalmente con letras mayúsculas (A, B, C...).',
            'hint' => 'Piensa en una tabla de números...',
            'easiness_factor' => 250,
            'interval' => 0,
            'repetitions' => 0,
            'next_review_at' => now(),
        ]);

        $flashcard2 = \App\Models\Flashcard::create([
            'user_id' => $admin->id,
            'subject_id' => $mathSubject->id,
            'topic_id' => $calculusTopic->id,
            'front' => '¿Qué significa que una función sea continua?',
            'back' => 'Una función es continua en un punto si: 1) está definida en ese punto, 2) existe el límite en ese punto, y 3) el límite coincide con el valor de la función.',
            'hint' => 'Son tres condiciones que involucran límites...',
            'easiness_factor' => 250,
            'interval' => 0,
            'repetitions' => 0,
            'next_review_at' => now(),
        ]);

        $flashcard3 = \App\Models\Flashcard::create([
            'user_id' => $admin->id,
            'subject_id' => $physicsSubject->id,
            'topic_id' => $mechanicsTopic->id,
            'front' => '¿Cuál es la segunda ley de Newton?',
            'back' => 'F = m·a (La fuerza es igual a la masa por la aceleración). Esta ley establece la relación entre la fuerza aplicada a un objeto, su masa y la aceleración resultante.',
            'easiness_factor' => 250,
            'interval' => 0,
            'repetitions' => 0,
            'next_review_at' => now(),
        ]);

        $this->command->info('Demo data created successfully!');
        $this->command->info('- 2 Subjects created');
        $this->command->info('- 3 Topics created');
        $this->command->info('- 2 Materials created');
        $this->command->info('- 3 Exercises created');
        $this->command->info('- 3 Flashcards created');
    }
}
