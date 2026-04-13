<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ActivityLogController extends Controller
{
    public function index()
    {
        return view('admin.activity.index');
    }

    public function export()
    {
        // TODO: Implement export
        return back()->with('info', 'Fitur export sedang dalam pengembangan.');
    }

    public function clear()
    {
        // TODO: Implement clear
        return back()->with('success', 'Log berhasil dibersihkan.');
    }

    public function show($id)
    {
        return view('admin.activity.show');
    }
}
