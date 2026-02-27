<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Project;

class ProjectApiController extends Controller
{
    public function index()
    {
        $projects = Project::visible()->ordered()->get([
            'id', 'name', 'url', 'url_label', 'image',
            'description', 'charge', 'dark_mode', 'position',
        ]);

        return response()->json($projects);
    }
}
