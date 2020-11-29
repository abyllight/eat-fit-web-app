<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function createExcel()
    {
        $now = Carbon::now()->format('Y-m-d H:i:s');
    }
}
