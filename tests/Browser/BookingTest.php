<?php

namespace Tests\Browser;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class BookingTest extends DuskTestCase
{
    /**
     * A Dusk test example.
     *
     * @return void
     */
    public function testExample()
    {
        $this->browse(function (Browser $browser) {
            $content = $browser->visit('https://www.zillow.com/homedetails/76-6212-Alii-Dr-APT-101-Kailua-Kona-HI-96740/71666125_zpid/')->waitFor('img', 30);
            var_dump($content);

        });
    }
}
