<?php

namespace App\Http\Controllers;
use App\Mail\TestMail;
use Illuminate\Support\Facades\Mail;

use Illuminate\Http\Request;

class TestMailController extends Controller
{
    
    public function send(){
        Mail::to('test@example.com')->send(new TestMail());
    }
}
