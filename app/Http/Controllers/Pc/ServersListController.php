<?php

namespace App\Http\Controllers\Pc;

use App\Http\Controllers\Controller;
use App\Models\Nas;
use Illuminate\Http\Request;
use App\Http\Resources\Pc\ServersResource;

class ServersListController extends Controller
{
    public function index(){
        $servers = Nas::where('is_enabled', 1)->where('in_app',1)->get();

        return ServersResource::collection($servers);
    }
}
