<?php

namespace Tests\Feature;

use Tests\TestCase;

class LocaleSwitchTest extends TestCase
{
    public function test_default_locale_is_uk(): void
    {
        $this->get('/')->assertOk();
        $this->assertSame('uk', app()->getLocale());
    }

    public function test_switch_to_en_persists_in_session(): void
    {
        $this->from('/')->get('/locale/en')->assertRedirect('/');
        $this->assertSame('en', session('locale'));

        $this->withSession(['locale' => 'en'])->get('/');
        $this->assertSame('en', app()->getLocale());
    }

    public function test_unsupported_locale_404s(): void
    {
        $this->from('/')->get('/locale/de')->assertNotFound();
    }
}
