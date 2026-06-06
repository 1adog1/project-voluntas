<?php

    namespace Ridley\Controllers\Unknown;

    class Controller implements \Ridley\Interfaces\Controller {
        
        private $databaseConnection;
        private $logger;
        private $isLoggedIn;
        private $configVariables;
        private $versionVariables;
        
        public function __construct(
            private \Ridley\Core\Dependencies\DependencyManager $dependencies
        ) {
            
            $this->databaseConnection = $this->dependencies->get("Database");
            $this->logger = $this->dependencies->get("Logging");
            $this->isLoggedIn = $this->dependencies->get("Login Status");
            $this->configVariables = $this->dependencies->get("Configuration Variables");
            $this->versionVariables = $this->dependencies->get("Version Variables");
            
            $rawURL = urldecode($_SERVER["REQUEST_URI"]);
            $parsedURL = parse_url($rawURL, PHP_URL_PATH);
            $parsedPath = preg_split(
                pattern: "@/@", 
                subject: $parsedURL, 
                flags: PREG_SPLIT_NO_EMPTY
            );
            
            if (count($parsedPath) >= 2 and $parsedPath[0] === "authenticate" and !$this->isLoggedIn) {
                
                $auth = new \Ridley\Core\Authorization\Base\AuthBase(
                    $this->logger, 
                    $this->databaseConnection, 
                    $this->configVariables,
                    $this->versionVariables
                );
                
                $auth->login("Default", $this->configVariables["Client Scopes"]);
                
            }
            
        }
        
    }

?>