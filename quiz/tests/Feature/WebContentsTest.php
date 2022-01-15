<?php

namespace Tests\Feature;

use Tests\TestCase;

class WebContentsTest extends TestCase
{
    public function testIndexRequest()
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $this->assertTrue(str_contains($response->content(), 'Choose your quiz!'));
    }

    public function testQuizRequest()
    {
        $response = $this->get('/_/1');

        $response->assertStatus(200);
        $this->assertTrue(str_contains($response->content(), 'Solve a Quiz'));
    }

    public function testNonExistentQuizRequest()
    {
        $response = $this->get('/_/-1');

        $response->assertStatus(200);
        $this->assertTrue(str_contains($response->content(), '404 | Quiz not found'));
    }

    public function testNewQuizRequest()
    {
        $response = $this->get('/new');

        $response->assertStatus(200);
    }
}
