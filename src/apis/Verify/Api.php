<?php

    namespace Ridley\Apis\Verify;

    class Api implements \Ridley\Interfaces\Api {
        
        private $databaseConnection;
        private $logger;
        private $configVariables;
        
        public function __construct(
            private \Ridley\Core\Dependencies\DependencyManager $dependencies
        ) {
            
            $this->databaseConnection = $this->dependencies->get("Database");
            $this->logger = $this->dependencies->get("Logging");
            $this->configVariables = $this->dependencies->get("Configuration Variables");
            
        }
        
    }

?>