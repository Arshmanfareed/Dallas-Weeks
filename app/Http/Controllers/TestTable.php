<?php

namespace App\Http\Controllers;

use App\Models\TestModel;
use Illuminate\Http\Request;

class TestTable extends Controller
{
<<<<<<< HEAD
    function insert_into_test_table($name, $slug)
=======
    function insert_into_test_table()
>>>>>>> seat_work
    {
        $test = new TestModel();
        $test->name = $name;
        $test->slug = $slug;
        $test->save();
    }
}
