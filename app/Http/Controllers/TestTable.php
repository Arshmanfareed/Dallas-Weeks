<?php

namespace App\Http\Controllers;

use App\Models\TestModel;
use Illuminate\Http\Request;

class TestTable extends Controller
{
    function insert_into_test_table($name, $slug)
    {
        $test = new TestModel();
        $test->name = $name;
        $test->slug = $slug;
        $test->save();
    }
}
