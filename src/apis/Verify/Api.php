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
                
                $output = new \SimpleXMLElement("<?xml version='1.0' encoding='utf-8'?><xml></xml>");
                
                foreach ($responseData as $eachResponse) {
                    
                    $newResponder = $output->addChild("responder");
                    $newResponder->addChild("verification-token", $eachResponse["verificationtoken"]);
                    $newResponder->addChild("character-name", $eachResponse["charactername"]);
                    $newResponder->addChild("core-name", $eachResponse["corename"]);
                    
                }
                
                Header("Content-Type: text/xml");
                echo $output->asXML();
                
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
                
                $output = new \SimpleXMLElement("<?xml version='1.0' encoding='utf-8'?><xml></xml>");
                
                foreach ($responseData as $eachResponse) {
                    
                    $newResponder = $output->addChild("responder");
                    $newResponder->addChild("verification-token", $eachResponse["verificationtoken"]);
                    $newResponder->addChild("character-name", $eachResponse["charactername"]);
                    $newResponder->addChild("core-name", $eachResponse["corename"]);
                    
                }
                
                Header("Content-Type: text/xml");
                echo $output->asXML();
                
            }
            else {
                
                header($_SERVER["SERVER_PROTOCOL"] . " 400 Bad Request");
                
            }
            
        }
        
    }

?>