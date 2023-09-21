<?php

namespace App\Http\Controllers;

use App\CentralLogics\Helpers;
use App\Models\BusinessSetting;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        /*$this->middleware('auth');*/
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        return view('home');
    }

    public function terms_and_conditions(Request $request)
    {
        $data = BusinessSetting::withOutGlobalScopes()->with('translations',function($query)use($request){
            $query->where('locale', $request->header('X-localization'));
        })->where('key','terms_and_conditions')->first();
    
        if ($request->expectsJson()) {
            if (count($data->translations) > 0) {
                return response()->json($data->translations[0]['value'],200);
            }else{
                return $data->value;
            }
        }
        $data = $data->value;
        return view('terms-and-conditions',compact('data'));
    }

    public function about_us(Request $request)
    {
        $data = BusinessSetting::withOutGlobalScopes()->with('translations',function($query)use($request){
            $query->where('locale', $request->header('X-localization'));
        })->where('key','about_us')->first();

        if ($request->expectsJson()) {
            if (count($data->translations) > 0) {
                return response()->json($data->translations[0]['value'],200);
            }else{
                return $data->value;
            }
        }

        $data = $data->value;

        return view('about-us',compact('data'));
    }

    public function contact_us()
    {
        return view('contact-us');
    }

    public function privacy_policy(Request $request)
    {
        $data = BusinessSetting::withOutGlobalScopes()->with('translations',function($query)use($request){
            $query->where('locale', $request->header('X-localization'));
        })->where('key','privacy_policy')->first();

        if ($request->expectsJson()) {
            if (count($data->translations) > 0) {
                return response()->json($data->translations[0]['value'],200);
            }else{
                return $data->value;
            }
        }

        $data = $data->value;

        return view('privacy-policy',compact('data'));
    }

    public static function get_settings($name)
    {
        $config = null;
        $data = BusinessSetting::where(['key' => $name])->first();
        if (isset($data)) {
            $config = json_decode($data['value'], true);
            if (is_null($config)) {
                $config = $data['value'];
            }
        }
        return $config;
    }
}
