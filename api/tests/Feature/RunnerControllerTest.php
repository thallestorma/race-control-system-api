<?php

namespace Tests\Controllers;

use Illuminate\Http\Response;
use Tests\TestCase;

class RunnerControllerTest extends TestCase
{

    /**
     * Test the listing of all results.
     *
     * @return void
     */
    public function testFindAll()
    {
        $response = $this->json('GET', '/api/runners');
        $response
            ->assertStatus(200)
            ->assertJsonStructure([
                '*' => [
                    'id',
                    'name',
                    'cpf',
                    'birthdate',
                    'created_at',
                    'updated_at'
                ]
            ]);
    }

    /**
     * Test the insertion of a new runner.
     *
     * @return void
     */
    public function testInsert()
    {
        $response = $this->json('POST', '/api/runners', [
            'name' => 'Fulano de Tal',
            'cpf' => '23861747006',
            'birthdate' => '19850510'
        ]);

        $response
            ->assertStatus(200)
            ->assertJsonStructure([
                'id',
                'name',
                'cpf',
                'birthdate',
                'created_at',
                'updated_at'
            ]);
    }

}
