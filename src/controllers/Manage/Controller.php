<?php

    namespace Ridley\Controllers\Manage;

    class Controller implements \Ridley\Interfaces\Controller {
        
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