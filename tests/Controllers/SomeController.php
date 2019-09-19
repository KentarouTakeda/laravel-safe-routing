<?php

namespace KentarouTakeda\SafeRouting\Tests\Controllers;

class SomeController {
    public function method() {
    }
    public function withReturn() {
        return [
            'foo' => 'var',
        ];
    }
}
