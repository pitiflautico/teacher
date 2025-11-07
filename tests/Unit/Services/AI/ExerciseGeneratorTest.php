<?php

namespace Tests\Unit\Services\AI;

use App\Models\Exercise;
use App\Models\Material;
use App\Models\Subject;
use App\Models\Topic;
use App\Models\User;
use App\Services\AI\AIManager;
use App\Services\AI\ExerciseGenerator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExerciseGeneratorTest extends TestCase
{
    use RefreshDatabase;

    protected ExerciseGenerator $generator;
    protected User $user;
    protected Subject $subject;
    protected Topic $topic;
    protected Material $material;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->actingAs($this->user);

        $this->subject = Subject::factory()->create();
        $this->topic = Topic::factory()->create([
            'subject_id' => $this->subject->id,
        ]);
        $this->material = Material::factory()->create([
            'user_id' => $this->user->id,
            'subject_id' => $this->subject->id,
            'topic_id' => $this->topic->id,
            'is_processed' => true,
            'extracted_text' => 'Sample educational content about mathematics. The quadratic formula is x = (-b ± √(b²-4ac)) / 2a.',
        ]);

        $this->generator = app(ExerciseGenerator::class);
    }

    /** @test */
    public function it_generates_exercises_from_material()
    {
        $this->markTestSkipped('Requires valid AI API credentials');

        $exercises = $this->generator->generateFromMaterial(
            $this->material,
            'multiple_choice',
            'medium',
            3
        );

        $this->assertIsArray($exercises);
        $this->assertNotEmpty($exercises);
        $this->assertCount(3, $exercises);

        foreach ($exercises as $exercise) {
            $this->assertInstanceOf(Exercise::class, $exercise);
            $this->assertEquals('multiple_choice', $exercise->type);
            $this->assertEquals('medium', $exercise->difficulty);
            $this->assertEquals($this->material->id, $exercise->material_id);
        }
    }

    /** @test */
    public function it_generates_exercises_from_topic()
    {
        $this->markTestSkipped('Requires valid AI API credentials');

        $exercises = $this->generator->generateFromTopic(
            $this->topic,
            'true_false',
            'easy',
            5
        );

        $this->assertIsArray($exercises);
        $this->assertNotEmpty($exercises);
        $this->assertCount(5, $exercises);

        foreach ($exercises as $exercise) {
            $this->assertInstanceOf(Exercise::class, $exercise);
            $this->assertEquals('true_false', $exercise->type);
            $this->assertEquals('easy', $exercise->difficulty);
            $this->assertEquals($this->topic->id, $exercise->topic_id);
        }
    }

    /** @test */
    public function it_validates_exercise_type()
    {
        $this->expectException(\InvalidArgumentException::class);

        $this->generator->generateFromMaterial(
            $this->material,
            'invalid_type',
            'medium',
            3
        );
    }

    /** @test */
    public function it_validates_difficulty_level()
    {
        $this->expectException(\InvalidArgumentException::class);

        $this->generator->generateFromMaterial(
            $this->material,
            'multiple_choice',
            'invalid_difficulty',
            3
        );
    }

    /** @test */
    public function it_validates_exercise_count()
    {
        $this->expectException(\InvalidArgumentException::class);

        $this->generator->generateFromMaterial(
            $this->material,
            'multiple_choice',
            'medium',
            0 // Invalid count
        );
    }

    /** @test */
    public function it_throws_exception_for_unprocessed_material()
    {
        $unprocessedMaterial = Material::factory()->create([
            'user_id' => $this->user->id,
            'is_processed' => false,
        ]);

        $this->expectException(\RuntimeException::class);

        $this->generator->generateFromMaterial(
            $unprocessedMaterial,
            'multiple_choice',
            'medium',
            3
        );
    }

    /** @test */
    public function it_includes_ai_metadata_in_exercises()
    {
        $this->markTestSkipped('Requires valid AI API credentials');

        $exercises = $this->generator->generateFromMaterial(
            $this->material,
            'multiple_choice',
            'medium',
            1
        );

        $exercise = $exercises[0];

        $this->assertNotNull($exercise->ai_metadata);
        $this->assertIsArray($exercise->ai_metadata);
        $this->assertArrayHasKey('provider', $exercise->ai_metadata);
        $this->assertArrayHasKey('model', $exercise->ai_metadata);
    }

    /** @test */
    public function it_generates_different_exercise_types()
    {
        $this->markTestSkipped('Requires valid AI API credentials');

        $types = ['multiple_choice', 'true_false', 'short_answer'];

        foreach ($types as $type) {
            $exercises = $this->generator->generateFromMaterial(
                $this->material,
                $type,
                'medium',
                1
            );

            $this->assertEquals($type, $exercises[0]->type);
        }
    }

    /** @test */
    public function it_generates_different_difficulty_levels()
    {
        $this->markTestSkipped('Requires valid AI API credentials');

        $difficulties = ['easy', 'medium', 'hard'];

        foreach ($difficulties as $difficulty) {
            $exercises = $this->generator->generateFromMaterial(
                $this->material,
                'multiple_choice',
                $difficulty,
                1
            );

            $this->assertEquals($difficulty, $exercises[0]->difficulty);
        }
    }

    /** @test */
    public function it_parses_json_response_correctly()
    {
        $this->markTestSkipped('Requires valid AI API credentials or mocked response');

        // Test that JSON parsing works correctly
        // Verify that malformed JSON is handled gracefully
    }

    /** @test */
    public function it_handles_api_errors_gracefully()
    {
        $this->markTestSkipped('Requires mocking AI API errors');

        // Test error handling for:
        // - Network errors
        // - Rate limiting
        // - Invalid responses
        // - Timeout
    }

    /** @test */
    public function it_detects_math_content()
    {
        $this->markTestSkipped('Requires valid AI API credentials');

        $exercises = $this->generator->generateFromMaterial(
            $this->material,
            'multiple_choice',
            'medium',
            1
        );

        // Material contains math formulas, so exercises should be flagged
        $this->assertTrue($exercises[0]->contains_math);
    }

    /** @test */
    public function it_associates_exercises_with_correct_relationships()
    {
        $this->markTestSkipped('Requires valid AI API credentials');

        $exercises = $this->generator->generateFromMaterial(
            $this->material,
            'multiple_choice',
            'medium',
            2
        );

        foreach ($exercises as $exercise) {
            $this->assertEquals($this->user->id, $exercise->user_id);
            $this->assertEquals($this->subject->id, $exercise->subject_id);
            $this->assertEquals($this->topic->id, $exercise->topic_id);
            $this->assertEquals($this->material->id, $exercise->material_id);
        }
    }
}
