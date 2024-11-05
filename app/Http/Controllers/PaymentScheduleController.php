<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PaymentScheduleController extends Controller
{
    function index(){
        return view('payment_schedule.index');
    }
}
