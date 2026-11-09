<?php

namespace App\Http\Controllers\Pc;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Resources\Pc\AccountsResource;

class AccountsController extends Controller
{
    public function accounts(){
        return AccountsResource::collection(auth()->user()->vpnAccounts);
    }

    public function user(){

    }
}
