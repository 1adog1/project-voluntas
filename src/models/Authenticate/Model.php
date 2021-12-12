<?php

    namespace Ridley\Models\Authenticate;

    class Model implements \Ridley\Interfaces\Model {
        
        public $hasAccess = false;
        public $pollDetails = [
            "Name" => null, 
            "Anonymity" => null, 
            "Limit Type" => null, 
            "Limit Amount" => null, 
            "Start Time" => null, 
            "End Time" => null
        ];
        
        private $controller;
        private $databaseConnection;
        private $characterStats;
        private $accessRoles;
        private $configVariables;
        private $urlData;

        public function __construct(
            private \Ridley\Core\Dependencies\DependencyManager $dependencies
        ) {
            
            $this->controller = $this->dependencies->get("Controller");
            $this->databaseConnection = $this->dependencies->get("Database");
            $this->characterStats = $this->dependencies->get("Character Stats");
            $this->accessRoles = $this->dependencies->get("Access Roles");
            $this->configVariables = $this->dependencies->get("Configuration Variables");
            $this->urlData = $this->dependencies->get("URL Data");
            
            $this->getDetails($this->urlData["Page Topic"]);
            
        }
        
        private function buildUserHash($pollRestrictionType) {
            
            if ($this->configVariables["Auth Type"] === "Neucore" and $pollRestrictionType === "Core Account") {
                
                $hashingString = "NEUCORE:ACCOUNT:" . $this->characterStats["Core ID"];
                
            }
            else {
                
                $hashingString = "EVE:CHARACTER:" . $this->characterStats["Character ID"];
                
            }
            
            return hash("sha256", $hashingString);
            
        }
        
        private function getDetails($pollID) {
            
            if (in_array("Submitter", $this->accessRoles)) {
                
                $pollQuery = $this->databaseConnection->prepare("SELECT * FROM polls WHERE id=:id;");
                $pollQuery->bindParam(":id", $pollID);
                $pollQuery->execute();
                $pollData = $pollQuery->fetchAll();
                
                if (!empty($pollData)) {
                    
                    foreach ($pollData as $eachPoll) {
                        
                        $this->pollDetails["Name"] = $eachPoll["name"];
                        $this->pollDetails["Anonymity"] = boolval($eachPoll["anonymity"]);
                        $this->pollDetails["Start Time"] = $eachPoll["starttime"];
                        $this->pollDetails["End Time"] = $eachPoll["endtime"];
                        
                        if (!is_null($eachPoll["percharacterlimit"])) {
                            
                            $this->pollDetails["Limit Type"] = "Player";
                            $this->pollDetails["Limit Amount"] = $eachPoll["percharacterlimit"];
                            
                        }
                        elseif (!is_null($eachPoll["percorelimit"])) {
                            
                            $this->pollDetails["Limit Type"] = "Core Account";
                            $this->pollDetails["Limit Amount"] = $eachPoll["percorelimit"];
                            
                        }
                        else {
                            
                            trigger_error("A poll was accessed that has no submission limits.", E_USER_ERROR);
                            
                        }
                        
                        $userHash = $this->buildUserHash($this->pollDetails["Limit Type"]);
                        
                        $allowedGroups = (!is_null($eachPoll["allowedroles"])) ? json_decode($eachPoll["allowedroles"]) : [];
                        
                        $coreGroups = (isset($this->characterStats["Core Groups"]) and !is_null($this->characterStats["Core Groups"])) ? $this->characterStats["Core Groups"] : [];
                        
                        if ($this->configVariables["Auth Type"] === "Neucore" and $this->pollDetails["Limit Type"] === "Core Account") {
                            
                            $responderName = $this->characterStats["Core Name"];
                            
                        }
                        else {
                            
                            $responderName = $this->characterStats["Character Name"];
                            
                        }
                        
                        $userDetails = $this->controller->getVoterDetails($userHash, $pollID);
                        
                        if ($this->controller->checkAccess(
                            $this->accessRoles, 
                            $coreGroups, 
                            $allowedGroups, 
                            $this->pollDetails["Limit Type"], 
                            $this->pollDetails["Limit Amount"], 
                            $userDetails["Counter"], 
                            $this->pollDetails["Start Time"], 
                            $this->pollDetails["End Time"]
                        )) {
                            
                            $this->controller->generateToken(
                                $userHash, 
                                $pollID, 
                                $this->pollDetails["Anonymity"], 
                                $this->pollDetails["Limit Type"], 
                                $responderName, 
                                $userDetails["Counter"]
                            );
                            
                            $this->hasAccess = true;
                            
                        }
                        
                    }
                    
                }
                
            }
            
        }
        
    }

?>