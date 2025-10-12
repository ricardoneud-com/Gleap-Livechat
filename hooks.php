<?php

use WHMCS\Database\Capsule;
use WHMCS\Session;

$enabled = Capsule::table('tbladdonmodules')
    ->where('module', 'Gleap')
    ->where('setting', 'GleapLiveChat-enable')
    ->where('value', 'on')
    ->count();

if (empty($enabled)) {
    return;
}

add_hook('ClientAreaFooterOutput', -1, function () {
    $token = Capsule::table('tbladdonmodules')
        ->where('module', 'Gleap')
        ->where('setting', 'GleapLiveChat-token')
        ->value('value');

    $cookies = Capsule::table('tbladdonmodules')
        ->where('module', 'Gleap')
        ->where('setting', 'GleapLiveChat-cookies')
        ->value('value') ?? 'off';
    
    $identityVerification = Capsule::table('tbladdonmodules')
        ->where('module', 'Gleap')
        ->where('setting', 'GleapLiveChat-identity_verification')
        ->value('value') ?? 'off';

    $identitySecret = Capsule::table('tbladdonmodules')
        ->where('module', 'Gleap')
        ->where('setting', 'GleapLiveChat-identity_verification_secret')
        ->value('value');

    $dataSyncValue = Capsule::table('tbladdonmodules')
        ->where('module', 'Gleap')
        ->where('setting', 'GleapLiveChat-datasync')
        ->value('value') ?? 'off';

    $userId = Session::get('uid');
    if (empty($token)) return;

    $useCookies = $cookies === 'on' ? 'true' : 'false';

    $jsCode = '';

    if ($dataSyncValue === 'on' && $userId) {
        $user = Capsule::table('tblclients')->where('id', $userId)->first();
        if ($user) {
            $name = addslashes($user->firstname);
            $email = addslashes($user->email);

            $identifyCode = '';
            if ($identityVerification === 'on' && !empty($identitySecret)) {
                $identifyCode = <<<JS
Gleap.identify("{$user->id}", {
    name: "{$name}",
    email: "{$email}"
}, "{$identitySecret}");
JS;
            } else {
                $identifyCode = <<<JS
Gleap.identify("{$user->id}", {
    name: "{$name}",
    email: "{$email}"
});
JS;
            }

            $jsCode = <<<JS
if (typeof Gleap !== "undefined") {
    {$identifyCode}

    Gleap.updateContact({
        name: "{$name}",
        email: "{$email}"
    });
}
JS;
        }
    }

    if ($dataSyncValue !== 'on' || !$userId) {
        $jsCode .= <<<JS

if (typeof Gleap !== "undefined") {
    Gleap.clearIdentity();
}
JS;
    }

    return <<<HTML
<script>
!function(){
    if(!(window.Gleap=window.Gleap||[]).invoked){
        window.GleapActions=[];
        const e=new Proxy({invoked:!0},{get:function(obj,prop){
            return "invoked"===prop?obj.invoked:function(){window.GleapActions.push({e:prop,a:Array.prototype.slice.call(arguments)})}
        },set:function(obj,prop,value){obj[prop]=value;return true}});
        window.Gleap=e;
        const head=document.getElementsByTagName("head")[0],
              script=document.createElement("script");
        script.type="text/javascript";
        script.async=true;
        script.src="https://sdk.gleap.io/latest/index.js";
        head.appendChild(script);
        Gleap.setUseCookies({$useCookies});
        Gleap.initialize("{$token}");
        {$jsCode}
    }
}();
</script>
HTML;
});