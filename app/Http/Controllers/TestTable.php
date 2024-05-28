<?php

namespace App\Http\Controllers;

use App\Models\TestModel;
use Illuminate\Http\Request;

class TestTable extends Controller
{
    function insert_into_test_table()
    {
        $test = new TestModel();
        $test->name = 'TESTING';
        $test->slug = 'Hey';
        $test->save();
    }
}
