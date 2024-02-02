<?php

use Inertia\Testing\AssertableInertia as Assert;

describe("Selecting source and target", function () {
    it("populates the props correctly for source provider page", function () {
        $header = 'Where would you like to transfer from?';

        $this->get('/transfer/source')
            ->assertInertia(fn (Assert $page) => $page
                ->component('Transfer/Source')
                ->has('buttonConfig', 2)
                ->where('header', $header)
            );
    });

    it("populates the props correctly for target page", function () {
        $header = 'Where would you like to transfer to?';
        $queryParams = '?source=spotify';

        $this->get('/transfer/target'.$queryParams)
            ->assertInertia(fn (Assert $page) => $page
                ->component('Transfer/Target')
                ->has('buttonConfig', 2)
                ->where('header', $header)
            );
    });

    it("redirects to source page, if correct query params are not provided", function () {
        $header = 'Where would you like to transfer to?';
        $this->get('/transfer/target')
            ->assertRedirectToRoute('transfer.source');
    });
});
