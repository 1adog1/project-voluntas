<?php

    declare(strict_types = 1);
    
    /*
        Define tables to add to the database here.
        
        The $siteDatabase->register method accepts the following arguments:
        
            A single $tableName string. 
            A variable amount of $tableColumns arrays.
            
        Each $tableColumns array can have the following keys:
        
            [REQUIRED] "Name" - The name of the column.
            [REQUIRED] "Type" - The SQL type of the column. 
            [OPTIONAL] "Special" - Any special modifiers for the column. 
            
        EXAMPLE:
        
            $siteDatabase->register(
                "table_name",
                ["Name" => "special_column", "Type" => "BIGINT", "Special" => "primary key AUTO_INCREMENT"],
                ["Name" => "column_two", "Type" => "TEXT"]
            );
            
    */
    
    $siteDatabase->register(
        "polls", 
        ["Name" => "id", "Type" => "TEXT"], 
        ["Name" => "name", "Type" => "TEXT"], 
        ["Name" => "creatorid", "Type" => "BIGINT"], 
        ["Name" => "verificationtoken", "Type" => "TEXT"], 
        ["Name" => "anonymity", "Type" => "TINYINT"], 
        ["Name" => "percharacterlimit", "Type" => "TINYINT"], 
        ["Name" => "percorelimit", "Type" => "TINYINT"], 
        ["Name" => "allowedroles", "Type" => "LONGTEXT"], 
        ["Name" => "starttime", "Type" => "BIGINT"], 
        ["Name" => "endtime", "Type" => "BIGINT"]
    );
    
    $siteDatabase->register(
        "usertokens", 
        ["Name" => "verificationtoken", "Type" => "TEXT"], 
        ["Name" => "pollid", "Type" => "TEXT"], 
        ["Name" => "charactername", "Type" => "TEXT", "Special" => "DEFAULT NULL"], 
        ["Name" => "corename", "Type" => "TEXT", "Special" => "DEFAULT NULL"]
    );
    
    $siteDatabase->register(
        "usercounters", 
        ["Name" => "userhash", "Type" => "TEXT"], 
        ["Name" => "pollid", "Type" => "TEXT"], 
        ["Name" => "counter", "Type" => "TINYINT"]
    );

?>