<?php

namespace Config;

class Middleware {
    protected $next;

    public function __construct($next = null) {
        $this->next = $next;
    }

    public function handle($request, $next) {
        if ($this->next instanceof Middleware) {
            return $this->next->handle($request, $next);
        }
        
        return $request;
    }
}