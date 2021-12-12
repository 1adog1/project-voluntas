<?php

    namespace Ridley\Apis\Manage;

    class Api implements \Ridley\Interfaces\Api {
        
        private $databaseConnection;
        private $logger;
        private $configVariables;
        private $character_id;
        
        public function __construct(
            private \Ridley\Core\Dependencies\DependencyManager $dependencies
        ) {
            
            $this->databaseConnection = $this->dependencies->get("Database");
            $this->logger = $this->dependencies->get("Logging");
            $this->configVariables = $this->dependencies->get("Configuration Variables");
            
            $this->character_id = $this->dependencies->get("Character Stats")["Character ID"];
            
            if (isset($_POST["Action"])) {
                
                if (
                    $_POST["Action"] == "Create_Poll" 
                    and isset($_POST["Name"]) 
                    and isset($_POST["Start_Date"]) 
                    and isset($_POST["End_Date"])
                    and (
                        isset($_POST["Anonymity"]) 
                        and in_array($_POST["Anonymity"], ["true", "false"])
                    )
                    and (
                        isset($_POST["Limit_Type"]) 
                        and in_array($_POST["Limit_Type"], ["Character", "Core"])
                    )
                    and (
                        isset($_POST["Submission_Limit"]) 
                        and is_numeric($_POST["Submission_Limit"]) 
                        and (int)$_POST["Submission_Limit"] >= 1 
                        and (int)$_POST["Submission_Limit"] <= 10
                    )
                ){
                    
                    $this->createPoll(
                        $_POST["Name"], 
                        ($_POST["Start_Date"] != "") ? $_POST["Start_Date"] : null, 
                        ($_POST["End_Date"] != "") ? $_POST["End_Date"] : null, 
                        ($_POST["Anonymity"] == "true") ? 1 : 0, 
                        $_POST["Limit_Type"], 
                        (int)$_POST["Submission_Limit"], 
                        (isset($_POST["Allowed_Core_Groups"]) and is_array($_POST["Allowed_Core_Groups"])) ? $_POST["Allowed_Core_Groups"] : null
                    );
                    
                }
                elseif (
                    $_POST["Action"] == "Delete_Poll" 
                    and isset($_POST["ID"]) 
                ) {
                    
                    $this->deletePoll(
                        $_POST["ID"]
                    );
                    
                }
                elseif (
                    $_POST["Action"] == "Get_Poll_Info" 
                    and isset($_POST["ID"]) 
                ) {
                    
                    $this->getPollInfo(
                        $_POST["ID"]
                    );
                    
                }
                else {
                    
                    header($_SERVER["SERVER_PROTOCOL"] . " 400 Bad Request");
                    trigger_error("No valid combination of action and required secondary arguments was received.", E_USER_ERROR);
                    
                }
                
            }
            else {
                
                header($_SERVER["SERVER_PROTOCOL"] . " 400 Bad Request");
                trigger_error("Request is missing the action argument.", E_USER_ERROR);
                
            }
            
        }
        
        private function createPoll(
            string $name, 
            ?string $start_date, 
            ?string $end_date, 
            int $anonymity, 
            string $limit_type, 
            int $submission_limit, 
            ?array $allowed_core_groups
        ) {
            
            $idBytes = random_bytes(32);
            $newID = bin2hex($idBytes);
            
            $tokenBytes = random_bytes(32);
            $newToken = bin2hex($tokenBytes);
            
            if (is_null($start_date)) {
                
                $start_timestamp = null;
                
            }
            elseif (strtotime($start_date) !== false) {
                
                $start_timestamp = strtotime($start_date);
                
            }
            else {
                
                header($_SERVER["SERVER_PROTOCOL"] . " 400 Bad Request");
                trigger_error("Invalid start date given.", E_USER_ERROR);
                
            }
            
            if (is_null($end_date)) {
                
                $end_timestamp = null;
                
            }
            elseif (strtotime($end_date) !== false) {
                
                $end_timestamp = strtotime($end_date) + 86400;
                
            }
            else {
                
                header($_SERVER["SERVER_PROTOCOL"] . " 400 Bad Request");
                trigger_error("Invalid end date given.", E_USER_ERROR);
                
            }
            
            if ($limit_type == "Core") {
                
                $preparedStatement = "INSERT INTO polls (id, name, creatorid, verificationtoken, anonymity, percorelimit, allowedroles, starttime, endtime) VALUES (:id, :name, :creatorid, :verificationtoken, :anonymity, :percorelimit, :allowedroles, :starttime, :endtime)";
                $limiter = ":percorelimit";
                
            }
            else {
                
                $preparedStatement = "INSERT INTO polls (id, name, creatorid, verificationtoken, anonymity, percharacterlimit, allowedroles, starttime, endtime) VALUES (:id, :name, :creatorid, :verificationtoken, :anonymity, :percharacterlimit, :allowedroles, :starttime, :endtime)";
                $limiter = ":percharacterlimit";
                
            }
            
            $creationQuery = $this->databaseConnection->prepare($preparedStatement);
            $creationQuery->bindParam(":id", $newID);
            $creationQuery->bindParam(":name", $name);
            $creationQuery->bindParam(":creatorid", $this->character_id);
            $creationQuery->bindParam(":verificationtoken", $newToken);
            $creationQuery->bindParam(":anonymity", $anonymity);
            $creationQuery->bindParam($limiter, $submission_limit);
            $creationQuery->bindValue(":allowedroles", (is_array($allowed_core_groups)) ? json_encode($allowed_core_groups) : null);
            $creationQuery->bindParam(":starttime", $start_timestamp);
            $creationQuery->bindParam(":endtime", $end_timestamp);
            $creationQuery->execute();
            
            $toOutput = [
                "ID" => $newID, 
                "Name" => $name, 
                "Token" => $newToken, 
                "Anonymity" => $anonymity, 
                "Limit Type" => $limit_type, 
                "Submission Limit" => $submission_limit, 
                "Allowed Roles" => $allowed_core_groups, 
                "Start Time" => $start_timestamp,
                "End Time" => $end_timestamp
            ];
            
            echo json_encode($toOutput);
            
        }
        
        private function deletePoll(
            string $id
        ) {
            
            $deletionQuery = $this->databaseConnection->prepare("DELETE FROM polls WHERE id=:id AND creatorid=:creatorid");
            $deletionQuery->bindParam(":id", $id);
            $deletionQuery->bindParam(":creatorid", $this->character_id);
            $deletionQuery->execute();
            
            $toOutput = [
                "Success" => true
            ];
            
            echo json_encode($toOutput);
            
        }
        
        private function getPollInfo(
            string $id
        ) {
            
            $getQuery = $this->databaseConnection->prepare("SELECT id, name, verificationtoken FROM polls WHERE id=:id AND creatorid=:creatorid");
            $getQuery->bindParam(":id", $id);
            $getQuery->bindParam(":creatorid", $this->character_id);
            $getQuery->execute();
            $getData = $getQuery->fetchAll();
            
            if (!empty($getData)) {
                
                foreach ($getData as $eachPoll) {
                    
                    $siteBaseURL = ((isset($_SERVER["HTTPS"]) and ($_SERVER["HTTPS"] == "on")) ? "https://" : "http://") . $_SERVER["HTTP_HOST"];
                    
                    $toOutput = [
                        "Poll Name" => $eachPoll["name"], 
                        "Poll ID" => htmlspecialchars($eachPoll["id"]), 
                        "Poll Verification Token" => htmlspecialchars($eachPoll["verificationtoken"]), 
                        "Site URL" => $siteBaseURL
                    ];
                    
                    echo json_encode($toOutput);
                    
                }
                
            }
            else {
                
                header($_SERVER["SERVER_PROTOCOL"] . " 400 Bad Request");
                trigger_error("Info requested for a non-existent poll.", E_USER_ERROR);
                
            }
            
        }
        
    }

?>