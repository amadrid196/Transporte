<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Setting;
use App\Models\FactorAgentSetting;

class SettingsController extends Controller
{
    //

    public function factorAgentSetting(Request $request)
    {
        $data = FactorAgentSetting::first();
        $pageTitle = "Invoice Email Setting";
        $url = route("factor-agent-setting-store");

        return view("settings.FactorAgentSetting", compact("data", "pageTitle", "url"));
    }

    public function factorAgentSetting_store(Request $request)
    {
        $factor_agent_setting = FactorAgentSetting::first();
        if($factor_agent_setting)
        {
            $factor_agent_setting->delete();
        }
        $email = $request->email;
        $pod = $request->pod;
        $cc = $request->cc;

        $factor_agent_setting = FactorAgentSetting::create(
            [
                'email'=>$email,
                "pod" => $pod,
                "cc" => $cc
            ]
        );
        return redirect('/setting/factoragentsetting')->with("message", __("Data saved successfully"));;
    }


}
