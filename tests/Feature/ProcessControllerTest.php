<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Process;
use App\Models\Type;
use App\Models\Field;

class ProcessControllerTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    // что поля могут быть добавлены к процессу и данные успешно сохраняются в базе данных.
    public function it_can_add_fields_to_a_process()
    {
        $process = Process::create(['name' => 'Test Process', 'description' => 'Test Description']);

        $fields = [
            ['name' => 'Text', 'type' => 'text', 'value' => 'Sample text'],
            ['name' => 'Number', 'type' => 'number', 'value' => 123, 'format' => '%.2f'],
            ['name' => 'Date', 'type' => 'date', 'value' => '2023-06-20']
        ];

        $response = $this->postJson('/api/processes/add-fields', [
            'process_name' => 'Test Process',
            'fields' => $fields
        ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('fields', ['name' => 'Text', 'value' => 'Sample text']);
        $this->assertDatabaseHas('fields', ['name' => 'Number', 'value' => 123]);
        $this->assertDatabaseHas('fields', ['name' => 'Date', 'value' => '2023-06-20']);
    }

    #[Test]
    // что возвращается ошибка, если процесс с указанным именем не найден при добавлении полей.
    public function it_returns_error_if_process_not_found_when_adding_fields()
    {
        $fields = [
            ['name' => 'Text', 'type' => 'text', 'value' => 'Sample text']
        ];

        $response = $this->postJson('/api/processes/add-fields', [
            'process_name' => 'Non Existing Process',
            'fields' => $fields
        ]);

        $response->assertStatus(404);
    }

    #[Test]
    // Что можно получить определенные поля, используя фильтрацию
    public function it_can_get_fields_of_a_process_with_filter()
    {
        $process = Process::create(['name' => 'Test Process', 'description' => 'Test Description']);
        $typeText = Type::create(['name' => 'text', 'format' => '', 'description' => 'Text type']);
        $typeNumber = Type::create(['name' => 'number', 'format' => '%.2f', 'description' => 'Number type']);
        $typeDate = Type::create(['name' => 'date', 'format' => 'Y-m-d', 'description' => 'Date type']);

        // Создание тестовых данных
        Field::create(['process_id' => $process->id, 'type_id' => $typeText->id, 'name' => 'Text', 'value' => 'Sample text', 'description' => '']);
        Field::create(['process_id' => $process->id, 'type_id' => $typeText->id, 'name' => 'Text2', 'value' => 'Sample text', 'description' => '']);

        Field::create(['process_id' => $process->id, 'type_id' => $typeNumber->id, 'name' => 'Number', 'value' => 123, 'description' => '']);
        Field::create(['process_id' => $process->id, 'type_id' => $typeDate->id, 'name' => 'Date', 'value' => '2023-06-20', 'description' => '']);

        $response = $this->postJson('/api/processes/get-fields', [
            'process_name' => 'Test Process',
            'page_size' => 12,
            'page' => 1,
            'field_name' => "Number",
            'field_type' => "number"
        ]);

        $response->assertStatus(200);
        $response->assertJsonCount(1, 'data');
        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'name'=>'Text2',
                    'value'
                ]
            ],
            'pagination' => [
                'current_page',
                'last_page',
                'per_page',
                'total',
                'next_page_url',
                'prev_page_url',
            ]
        ]);
    }

    #[Test]
    // что поля могут быть получены для процесса с использованием пагинации и возвращаются корректные данные.
    public function it_can_get_fields_of_a_process_with_pagination()
    {
        $process = Process::create(['name' => 'Test Process', 'description' => 'Test Description']);
        $typeText = Type::create(['name' => 'text', 'format' => '', 'description' => 'Text type']);
        $typeNumber = Type::create(['name' => 'number', 'format' => '%.2f', 'description' => 'Number type']);
        $typeDate = Type::create(['name' => 'date', 'format' => 'Y-m-d', 'description' => 'Date type']);

        // Создание тестовых данных
        Field::create(['process_id' => $process->id, 'type_id' => $typeText->id, 'name' => 'Text', 'value' => 'Sample text', 'description' => '']);
        Field::create(['process_id' => $process->id, 'type_id' => $typeNumber->id, 'name' => 'Number', 'value' => 123, 'description' => '']);
        Field::create(['process_id' => $process->id, 'type_id' => $typeDate->id, 'name' => 'Date', 'value' => '2023-06-20', 'description' => '']);

        $response = $this->postJson('/api/processes/get-fields', [
            'process_name' => 'Test Process',
            'page_size' => 12,
            'page' => 1
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'name',
                    'value'
                ]
            ],
            'pagination' => [
                'current_page',
                'last_page',
                'per_page',
                'total',
                'next_page_url',
                'prev_page_url',
            ]
        ]);
    }

    #[Test]
    // что возвращается ошибка, если процесс с указанным именем не найден при получении полей.
    public function it_returns_error_if_process_not_found_when_getting_fields()
    {
        $response = $this->postJson('/api/processes/add-fields', [
            'process_name' => 'Non Existing Process',
            'page_size' => 12,
            'page' => 1
        ]);
        $response->assertStatus(404);
    }
}
