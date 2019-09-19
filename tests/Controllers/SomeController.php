<?php

namespace KentarouTakeda\SafeRouting\Tests\Controllers;
use Illuminate\Http\Request;

class SomeController {
    public function method() {
    }
    public function withReturn() {
        return [
            'foo' => 'var',
        ];
    }
    public function get(Request $request) {
        return $request->query();
    }
    public function post(Request $request) {
        return $request->post();
    }
}
