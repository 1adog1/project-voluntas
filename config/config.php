<?php

    $configVariables = [];

    $configData = parse_ini_file(__DIR__ . "/config.ini");
    
    //EVE AUTHENTICATION CONFIGURATION
    $configVariables["Client ID"] = (isset($_ENV["Client_ID"])) ? $_ENV["Client_ID"] : $configData["ClientID"];
    $configVariables["Client Secret"] = (isset($_ENV["Client_Secret"])) ? $_ENV["Client_Secret"] : $configData["ClientSecret"];
    $configVariables["Client Scopes"] = (isset($_ENV["Client_Scopes"])) ? $_ENV["Client_Scopes"] : $configData["ClientScopes"];
    $configVariables["Default Scopes"] = (isset($_ENV["Default_Scopes"])) ? $_ENV["Default_Scopes"] : $configData["DefaultScopes"];
    $configVariables["Client Redirect"] = (isset($_ENV["Client_Redirect"])) ? $_ENV["Client_Redirect"] : $configData["ClientRedirect"];
    $configVariables["Auth Type"] = (isset($_ENV["Auth_Type"])) ? $_ENV["Auth_Type"] : $configData["AuthType"];
    $configVariables["Super Admins"] = (isset($_ENV["Super_Admins"])) ? explode(",", str_replace(" ", "", $_ENV["Super_Admins"])) : explode(",", str_replace(" ", "", $configData["SuperAdmins"]));
    
    //NEUCORE AUTHENTICATION CONFIGURATION
    $configVariables["NeuCore ID"] = (isset($_ENV["App_ID"])) ? $_ENV["App_ID"] : $configData["AppID"];
    $configVariables["NeuCore Secret"] = (isset($_ENV["App_Secret"])) ? $_ENV["App_Secret"] : $configData["AppSecret"];
    $configVariables["NeuCore URL"] = (isset($_ENV["App_URL"])) ? $_ENV["App_URL"] : $configData["AppURL"];
    
    //DATABASE SERVER CONFIGURATION
    $configVariables["Database Server"] = (isset($_ENV["Database_Server"])) ? $_ENV["Database_Server"] : ($configData["DatabaseServer"] . ":" . $configData["DatabasePort"]);
    $configVariables["Database Username"] = (isset($_ENV["Database_Username"])) ? $_ENV["Database_Username"] : $configData["DatabaseUsername"];
    $configVariables["Database Password"] = (isset($_ENV["Database_Password"])) ? $_ENV["Database_Password"] : $configData["DatabasePassword"];
    
    //DATABASE NAME CONFIGURATION
    $configVariables["Database Name"] = (isset($_ENV["Database_Name"])) ? $_ENV["Database_Name"] : $configData["DatabaseName"];
    
    //JawsDB Maria Support (For Heroku)
    if (isset($_ENV["JAWSDB_MARIA_URL"])) {
        
        $jawsDBURL = parse_url($_ENV["JAWSDB_MARIA_URL"]);
        
        $configVariables["Database Server"] = $jawsDBURL["host"];
        $configVariables["Database Username"] = $jawsDBURL["user"];
        $configVariables["Database Password"] = $jawsDBURL["pass"];
        $configVariables["Database Name"] = ltrim($jawsDBURL["path"], "/");
        
    }
    
    //SITE CONFIGURATION
    $configVariables["Auth Cookie Name"] = (isset($_ENV["Auth_Cookie_Name"])) ? $_ENV["Auth_Cookie_Name"] : $configData["AuthCookieName"];
    $configVariables["Session Time"] = (isset($_ENV["Session_Time"])) ? $_ENV["Session_Time"] : $configData["SessionTime"];
    $configVariables["Auth Cache Time"] = (isset($_ENV["Auth_Cache_Time"])) ? $_ENV["Auth_Cache_Time"] : $configData["AuthCacheTime"];
    $configVariables["Max Table Rows"] = (isset($_ENV["Max_Table_Rows"])) ? $_ENV["Max_Table_Rows"] : $configData["MaxTableRows"];
    $configVariables["Store Visitor IPs"] = (isset($_ENV["Store_Visitor_IPs"])) ? boolval($_ENV["Store_Visitor_IPs"]) : boolval($configData["StoreVisitorIPs"]);
    
?>