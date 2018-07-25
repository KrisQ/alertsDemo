<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\User;
use App\Alert;

class AlertController extends Controller
{
    //

    public function create(Request $request){
    	$alert = new Alert;
    	$alert->user_id = $request->user_id;
    	$alert->date_range = $request->date_range;
    	$alert->customer_id = $request->customer_id;
    	$alert->short_description = $request->short_description;
    	$alert->long_description = $request->long_description;
    	$alert->status = $request->status;
    	$alert->save();
    	return $alert;
    }

    public function getAll(){
        $user = Auth::user();
        $users = User::all();
    	$alerts = Alert::orderBy('created_at', 'desc')->paginate(6);
    	return [
            "user" => $user,
            "customers" => $users,
            "alerts" => $alerts
        ];
	}
	
    public function getOne($id){
    	$alert = Alert::findOrfail($id);
    	return $alert;
    }

    public function update(Request $request){
    	$alert = Alert::findOrFail($request->id);
		$alert->date_range = $request->date_range;
    	$alert->short_description = $request->short_description;
    	$alert->long_description = $request->long_description;
    	$alert->status = $request->status;
    	$alert->save();
    	return $alert;
    } 

    public function delete($id){
    	$alert = Alert::findOrFail($id)->delete();
    	return ["Message", "destroyed" ];
	}
	
	public function getAllWithoutPaginate(){
		$user = Auth::user();
        $users = User::all();
    	$alerts = Alert::orderBy('created_at', 'desc')->get();
    	return [
            "user" => $user,
            "customers" => $users,
            "alerts" => $alerts
        ];
	}

	public function getAlerts(){
    	$alerts = Alert::all();
    	return $alerts;
	}



}
