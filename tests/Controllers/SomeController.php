<?php

namespace KentarouTakeda\SafeRouting\Tests\Controllers;
use Illuminate\Http\Request;

class SomeController {
    public function method() {
    }
    public function withReturn() {
        return [
            'foo' => 'bar',
        ];
    }
    public function get(Request $request) {
        return $request->query();
    }
    public function post(Request $request) {
        return $request->post();
    }
    public function returnA() {
        return [
            'data' => 'A',
        ];
    }
    public function returnB() {
        return [
            'data' => 'B',
        ];
    }
    public function returnC() {
        return [
            'data' => 'C',
        ];
    }
}
