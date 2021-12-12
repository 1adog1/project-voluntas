<?php

    namespace Ridley\Controllers\Authenticate;

    class Controller implements \Ridley\Interfaces\Controller {
        
        public $userToken = null;
        
        private $databaseConnection;
        private $logger;
        
        
        public function __construct(
            private \Ridley\Core\Dependencies\DependencyManager $dependencies
        ) {
            
            $this->databaseConnection = $this->dependencies->get("Database");
            $this->logger = $this->dependencies->get("Logging");
            
        }
        
        public function getVoterDetails($userHash, $pollID) {
            
            $details = [
                "Found" => false,
                "Counter" => 0
            ];
            
            $voterQuery = $this->databaseConnection->prepare("SELECT * FROM usercounters WHERE userhash=:userhash AND pollid=:pollid;");
            $voterQuery->bindParam(":userhash", $userHash);
            $voterQuery->bindParam(":pollid", $pollID);
            $voterQuery->execute();
            $voterData = $voterQuery->fetchAll();
            
            if (!empty($voterData)) {
                
                foreach ($voterData as $eachVoter) {
                    
                    $details["Found"] = true;
                    $details["Counter"] = $eachVoter["counter"];
                    
                }
                
            }
            
            return $details;
            
        }
        
        public function checkAccess(
            $accessRoles, 
            $coreGroups, 
            $allowedGroups, 
            $limitType, 
            $limitAmount, 
            $existingResponses, 
            $startTime, 
            $endTime
        ) {
            
            $currentTime = time();
            
            return (
                in_array("Submitter", $accessRoles) 
                and (
                    empty($allowedGroups) 
                    or count(array_intersect($coreGroups, $allowedGroups)) !== 0
                )
                and (
                    is_null($startTime) 
                    or $currentTime >= $startTime
                )
                and (
                    is_null($endTime) 
                    or $currentTime <= $endTime
                )
                and (
                    $existingResponses < $limitAmount
                )
            );
            
        }
        
        public function generateToken(
            $userHash, 
            $pollID, 
            $isAnonymous, 
            $nameType, 
            $nameValue, 
            $expectedResponses
        ) {
            
            $currentDetails = $this->getVoterDetails($userHash, $pollID); 
            
            if ($currentDetails["Counter"] === $expectedResponses) {
                
                if ($currentDetails["Found"]) {
                    
                    $counterIncrementer = $this->databaseConnection->prepare("UPDATE usercounters SET counter = counter + 1 WHERE userhash=:userhash AND pollid=:pollid;");
                    $counterIncrementer->bindParam(":userhash", $userHash);
                    $counterIncrementer->bindParam(":pollid", $pollID);
                    $counterIncrementer->execute();
                    
                }
                else {
                    
                    $counterMaker = $this->databaseConnection->prepare("INSERT INTO usercounters (userhash, pollid, counter) VALUES (:userhash, :pollid, :counter);");
                    $counterMaker->bindParam(":userhash", $userHash);
                    $counterMaker->bindParam(":pollid", $pollID);
                    $counterMaker->bindValue(":counter", 1);
                    $counterMaker->execute();
                    
                }
                
                $tokenBytes = random_bytes(16);
                $this->userToken = bin2hex($tokenBytes);
                
                if (!$isAnonymous) {
                    
                    if ($nameType === "Core Account") {
                        
                        $coreName = $nameValue;
                        $characterName = null;
                        
                    }
                    elseif ($nameType === "Player") {
                        
                        $characterName = $nameValue;
                        $coreName = null;
                        
                    }
                    else {
                        
                        $characterName = null;
                        $coreName = null;
                        
                    }
                    
                }
                else {
                    
                    $characterName = null;
                    $coreName = null;
                    
                }
                
                $tokenInsert = $this->databaseConnection->prepare("INSERT INTO usertokens (verificationtoken, pollid, charactername, corename) VALUES (:verificationtoken, :pollid, :charactername, :corename);");
                $tokenInsert->bindParam(":verificationtoken", $this->userToken);
                $tokenInsert->bindParam(":pollid", $pollID);
                $tokenInsert->bindParam(":charactername", $characterName);
                $tokenInsert->bindParam(":corename", $coreName);
                $tokenInsert->execute();
                
            }
            else {
                
                trigger_error("Race condition occurred while trying to generate a user token.", E_USER_ERROR);
                
            }
            
        }
        
    }

?>