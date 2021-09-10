<?php

    namespace Ridley\Views\Verify;
    
    class View implements \Ridley\Interfaces\View {
        
        public function __construct(
            private \Ridley\Core\Dependencies\DependencyManager $dependencies
        ) {
            
            header("Location: /unknown/");
            die();
            
        }
        
        public function renderContent() {
            
        }
        
    }

?>