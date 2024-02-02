<?php

describe("Home Page", function () {
    it('loads home pages', function () {
        $this->get('/')->assertStatus(200);
    });
});

