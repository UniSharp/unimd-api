<?php

namespace App\Swoole\Auth\Guards;

use App\Auth\Guards\JwtAuthGuard;

class WebsocketGuard extends JwtAuthGuard
{
    /**
     * Get the currently authenticated user.
     *
     * @return \Illuminate\Contracts\Auth\Authenticatable|null
     */
    public function user()
    {
        if ($this->jwt->setRequest($this->request)->getToken() && $this->jwt->check()) {
            $id = $this->jwt->payload()->get('sub');

            return $this->user = $this->provider->retrieveById($id);
        }
    }
}
