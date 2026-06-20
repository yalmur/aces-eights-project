<?php

namespace Tests\Feature;

use Tests\TestCase;

class PartyHallTest extends TestCase
{
    public function test_party_hall_redirects_to_external_hire_page(): void
    {
        $this->get('/party-hall')->assertRedirect('https://www.acesandeightssaloonbar.com/hire/');
    }
}
