<?php

    namespace Ridley\Models\Manage;

    class Model implements \Ridley\Interfaces\Model {
        
        private $controller;
        private $databaseConnection;
        private $character_id;
        
        public $polls = [
            "Active" => [], 
            "Upcoming" => [], 
            "Complete" => []
        ];
        
        public function __construct(
            private \Ridley\Core\Dependencies\DependencyManager $dependencies
        ) {
            
            $this->controller = $this->dependencies->get("Controller");
            $this->databaseConnection = $this->dependencies->get("Database");
            $this->character_id = $this->dependencies->get("Character Stats")["Character ID"];
            
            $this->populatePolls();
            
        }
        
        private function populatePolls() {
            
            $currentTime = time();
            
            $checkQuery = $this->databaseConnection->prepare("SELECT id, name, anonymity, percharacterlimit, percorelimit, allowedroles, starttime, endtime FROM polls WHERE creatorid=:creatorid;");
            $checkQuery->bindParam(":creatorid", $this->character_id);
            $checkQuery->execute();
            $checkData = $checkQuery->fetchAll();
            
            if (!empty($checkData)) {
                
                foreach ($checkData as $eachPoll) {
                    
                    if (!is_null($eachPoll["starttime"]) and $eachPoll["starttime"] > $currentTime) {
                        
                        $this->polls["Upcoming"][] = $eachPoll;
                        
                    }
                    elseif (!is_null($eachPoll["endtime"]) and $eachPoll["endtime"] < $currentTime) {
                        
                        $this->polls["Complete"][] = $eachPoll;
                        
                    }
                    else {
                        
                        $this->polls["Active"][] = $eachPoll;
                        
                    }
                    
                }
                
            }
            
        }
        
    }

?>