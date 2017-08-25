<?php

if (! function_exists('uni_output')) {
    function uni_output($content)
    {
        app('output')->writeln($content);
    }
}

if (! function_exists('uni_table')) {
    function uni_table($table)
    {
        return app('swoole.table')->{$table};
    }
}
