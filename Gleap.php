<?php

if (!defined("WHMCS")) {
    die("This file cannot be accessed directly");
}

function Gleap_config() {
    $authorLink = "https://ricardoneud.com";
    return array(
        "name" => "Gleap",
        "description" => "The AI-powered customer support OS",
        "version" => "1.0",
        "author" => '<a href="' . $authorLink . '" style="text-decoration: none; display: inline-flex; align-items: center;">Ricardoneud.com</a>',
        "fields" => array(
            "GleapLiveChat-enable" => array(
                "FriendlyName" => "Enable livechat?", 
                "Type" => "yesno", 
                "Size" => "55", 
                "Description" => "A quick way to enable or disable the chat on your website", 
                "Default" => "", 
            ),
            "GleapLiveChat-token" => array(
                "FriendlyName" => "API Key",
                "Type" => "text",
                "Size" => "90",
                "Description" => "Enter your Gleap API Key",
                "Default" => "",
            ),
            "GleapLiveChat-cookies" => array(
                "FriendlyName" => "Use Cookies",
                "Type" => "yesno",
                "Size" => "55",
                "Description" => "Enable cookies to maintain sessions across all subdomains. Disabling this requires separate user identification per subdomain.",
                "Default" => "",
            ),
            "GleapLiveChat-identity_verification" => array(
                "FriendlyName" => "Enforce Identity Verification",
                "Type" => "yesno",
                "Size" => "55",
                "Description" => "Enforce identity verification to prevent third parties from impersonating logged-in users. This is optional and only required if 'Enforce Identity' is enabled in Gleap.",
                "Default" => "",
            ),
            "GleapLiveChat-identity_verification_secret" => array(
                "FriendlyName" => "Identity Verification Secret",
                "Type" => "password",
                "Size" => "99",
                "Description" => "Identity Secret used to authenticate requests for logged-in users. This is optional and only required if 'Enforce Identity' is enabled in Gleap.",
                "Default" => "",
            ),
            "GleapLiveChat-datasync" => array(
                "FriendlyName" => "Enable Data Sync",
                "Type" => "yesno",
                "Size" => "55",
                "Description" => "If enabled, WHMCS client data (first name, email, user ID, signup date) will be automatically synced with Gleap. "
                    . "This ensures personalization in the live chat (e.g., showing 'Hi John' instead of 'Hi') and enables automatic authorization for Gleap services.",
                "Default" => "",
            ),
        )
    );
}

function Gleap_activate() {
    try {
        return ['status' => 'success', 'description' => 'Gleap add-on has been activated.'];
    } catch (\Exception $e) {
        return ['status' => 'error', 'description' => 'Error creating tables: ' . $e->getMessage()];
    }
}

function Gleap_deactivate() {
    try {
        return ['status' => 'success', 'description' => 'Gleap add-on has been deactivated.'];
    } catch (\Exception $e) {
        return ['status' => 'error', 'description' => 'Error dropping tables: ' . $e->getMessage()];
    }
}

?>