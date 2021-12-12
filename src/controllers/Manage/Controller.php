<?php

    namespace Ridley\Controllers\Manage;

    class Controller implements \Ridley\Interfaces\Controller {
        
        private $databaseConnection;
        private $logger;
        private $configVariables;
        
        public $coreGroups = [];
        
        public function __construct(
            private \Ridley\Core\Dependencies\DependencyManager $dependencies
        ) {
            
            $this->databaseConnection = $this->dependencies->get("Database");
            $this->logger = $this->dependencies->get("Logging");
            $this->configVariables = $this->dependencies->get("Configuration Variables");
            
            if ($this->configVariables["Auth Type"] == "Neucore") {
                
                $this->populateCoreGroups();
                
            }
            
        }
        
        private function populateCoreGroups() {
            
            $neucoreToken = base64_encode($this->configVariables["NeuCore ID"] . ":" . $this->configVariables["NeuCore Secret"]);
        
            $appRequestURL = $this->configVariables["NeuCore URL"] . "api/app/v1/show";
            
            $appRequestOptions = ["http" => ["ignore_errors" => true, "method" => "GET", "header" => ["Content-Type:application/json", "Authorization: Bearer " . $neucoreToken]]];
            $appRequestContext = stream_context_create($appRequestOptions);
            
            $appResponse = file_get_contents($appRequestURL, false, $appRequestContext);
            
            $appStatus = $http_response_header[0];
            
            if (str_contains($appStatus, "200")) {
            
                $appResponseData = json_decode($appResponse, true);
                
                foreach ($appResponseData["groups"] as $eachGroup) {
                    
                    $this->coreGroups[] = $eachGroup["name"];
                    
                }
                
            }
            
        }
        
    }

?>