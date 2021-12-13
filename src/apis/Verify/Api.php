<?php

    namespace Ridley\Apis\Verify;

    class Api implements \Ridley\Interfaces\Api {
        
        private $databaseConnection;
        private $logger;
        private $configVariables;
        private $urlData;
        
        public function __construct(
            private \Ridley\Core\Dependencies\DependencyManager $dependencies
        ) {
            
            $this->databaseConnection = $this->dependencies->get("Database");
            $this->logger = $this->dependencies->get("Logging");
            $this->configVariables = $this->dependencies->get("Configuration Variables");
            $this->urlData = $this->dependencies->get("URL Data");
            
            if ($this->urlData["Page Topic"] !== false and $this->urlData["Page Number"] !== false) {
                
                $this->verifyResponse($this->urlData["Page Topic"], $this->urlData["Page Number"]);
                
            }
            elseif ($this->urlData["Page Topic"] !== false) {
                
                $this->getAllResponses($this->urlData["Page Topic"]);
                
            }
            else {
                
                header($_SERVER["SERVER_PROTOCOL"] . " 400 Bad Request");
                trigger_error("Invalid URL Format for a Verification Request.", E_USER_ERROR);
                
            }
            
        }
        
        private function getAllResponses($verificationToken) {
            
            $responseQuery = $this->databaseConnection->prepare("SELECT usertokens.verificationtoken, usertokens.charactername, usertokens.corename FROM usertokens INNER JOIN polls ON usertokens.pollid = polls.id WHERE polls.verificationtoken = :verificationtoken;");
            $responseQuery->bindParam(":verificationtoken", $verificationToken);
            $responseQuery->execute();
            $responseData = $responseQuery->fetchAll();
            
            if (!empty($responseData)) {
                
                header("Content-type: text/csv");
                header("Cache-Control: no-store, no-cache");
                header("Content-Disposition: attachment; filename=response_list.csv");
                
                $output = fopen("php://output", "w");
                fputcsv($output, [
                    "Verification Token", 
                    "Character Name", 
                    "Core Account"
                ]);
                
                foreach ($responseData as $eachResponse) {
                    
                    fputcsv($output, [
                        $eachResponse["verificationtoken"], 
                        $eachResponse["charactername"], 
                        $eachResponse["corename"]
                    ]);
                    
                }
                
                fclose($output);
                
            }
            else {
                
                header($_SERVER["SERVER_PROTOCOL"] . " 400 Bad Request");
                
            }
            
        }
        
        private function verifyResponse($verificationToken, $userToken) {
            
            $responseQuery = $this->databaseConnection->prepare("SELECT usertokens.verificationtoken, usertokens.charactername, usertokens.corename FROM usertokens INNER JOIN polls ON usertokens.pollid = polls.id WHERE polls.verificationtoken = :verificationtoken AND usertokens.verificationtoken = :usertoken;");
            $responseQuery->bindParam(":verificationtoken", $verificationToken);
            $responseQuery->bindParam(":usertoken", $userToken);
            $responseQuery->execute();
            $responseData = $responseQuery->fetchAll();
            
            if (!empty($responseData)) {
                
                header("Content-type: text/csv");
                header("Cache-Control: no-store, no-cache");
                header("Content-Disposition: attachment; filename=response_verification.csv");

                $output = fopen("php://output", "w");
                
                foreach ($responseData as $eachResponse) {
                    
                    fputcsv($output, [
                        "Verification Token", 
                        $eachResponse["verificationtoken"], 
                        "Character Name", 
                        $eachResponse["charactername"], 
                        "Core Account", 
                        $eachResponse["corename"]
                    ]);
                    
                }
                
                fclose($output);
                
            }
            else {
                
                header($_SERVER["SERVER_PROTOCOL"] . " 400 Bad Request");
                
            }
            
        }
        
    }

?>