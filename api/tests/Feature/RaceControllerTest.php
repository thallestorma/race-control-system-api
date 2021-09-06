<?php

namespace Tests\Controllers;

use Illuminate\Http\Response;
use Tests\TestCase;

class RaceControllerTest extends TestCase
{

    /**
     * Test the listing of all results sorted by duration for each race.
     *
     * @return void
     */
    public function testListGeneralResult()
    {
        $response = $this->json('GET', '/api/races');
        $response
            ->assertStatus(200)
            ->assertJsonStructure([
                '*' => [
                    'id_race',
                    'type',
                    'results' => [
                        '*' => [
                            'id_runner', 'runner_age', 'runner_name', 'position'
                        ]
                    ]
                ]
            ]);
    }

    /**
     * Test the listing of all results sorted by duration and organized by age category for each race.
     *
     * @return void
     */
    public function testListResultByAge()
    {
        $response = $this->json('GET', '/api/races/list-result-by-age');
        $response
            ->assertStatus(200)
            ->assertJsonStructure([
                '*' => [
                    'id_race',
                    'type',
                    'results' => [
                        'cat_18_25' => [
                            '*' => [
                                'id_runner', 'runner_age', 'runner_name', 'position'
                            ]
                        ],
                        'cat_26_35' => [
                            '*' => [
                                'id_runner', 'runner_age', 'runner_name', 'position'
                            ]
                        ],
                        'cat_36_45' => [
                            '*' => [
                                'id_runner', 'runner_age', 'runner_name', 'position'
                            ]
                        ],
                        'cat_46_55' => [
                            '*' => [
                                'id_runner', 'runner_age', 'runner_name', 'position'
                            ]
                        ],
                        'cat_55' => [
                            '*' => [
                                'id_runner', 'runner_age', 'runner_name', 'position'
                            ]
                        ],
                    ]
                ]
            ]);
    }

    /**
     * Test the insertion of a new race.
     *
     * @return void
     */
    public function testInsert()
    {
        $response = $this->json('POST', '/api/races', [
            'type' => '10km',
            'event_date' => '20211210'
        ]);

        $response
            ->assertStatus(200)
            ->assertJsonStructure([
                'id',
                'type',
                'event_date',
                'updated_at',
                'created_at'
            ]);
    }

    /**
     * Test the insertion of a new runner in a race.
     *
     * @return void
     */
    public function testAddRunner()
    {
        $response = $this->json('POST', '/api/races/add-runner', [
            'id_race' => '1',
            'id_runner' => '1'
        ]);

        $response
            ->assertStatus(200)
            ->assertJsonStructure([
                'id_race',
                'id_race',
                'start_time',
                'finish_time'
            ]);
    }

    /**
     * Test the insertion of results of a runner in a race.
     *
     * @return void
     */
    public function testInsertResults()
    {
        $response = $this->json('POST', '/api/races/add-results', [
            'id_race' => '1',
            'id_runner' => '1',
            'start_time' => '13:00:00',
            'finish_time' => '14:00:00'
        ]);

        $response
            ->assertStatus(200)
            ->assertJsonStructure([
                'id_race',
                'id_race',
                'start_time',
                'finish_time'
            ]);
    }
}
