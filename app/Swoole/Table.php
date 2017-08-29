<?php

namespace App\Swoole;

use Swoole\Table as SwooleTable;

class Table
{
    public $users;
    public $diffs;

    public function __construct()
    {
        $this->initUsers();
        $this->initDiffs();
    }

    protected function initUsers()
    {
        $this->users = new SwooleTable(config('swoole.websocket.max_online_users'));
        $this->users->column('id', SwooleTable::TYPE_INT, 4);
        $this->users->column('name', SwooleTable::TYPE_STRING, 32);
        $this->users->column('room_id', SwooleTable::TYPE_INT, 8);
        $this->users->create();
    }

    protected function initDiffs()
    {
        // TODO: check table size and max column limit
        $this->diffs = new SwooleTable(config('swoole.websocket.max_online_notes'));
        $this->diffs->column('updated_at', SwooleTable::TYPE_INT, 8);
        $this->diffs->column('content', SwooleTable::TYPE_STRING, 65536);
        $this->diffs->create();
    }
}