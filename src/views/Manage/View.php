<?php

    namespace Ridley\Views\Manage;

    class Templates {
        
        protected function mainTemplate() {
            ?>
            
            
            
            <?php
        }
        
    }

    class View extends Templates implements \Ridley\Interfaces\View {
        
        public function __construct(
            private \Ridley\Core\Dependencies\DependencyManager $dependencies
        ) {
            
        }
        
        public function renderContent() {
            
            $this->mainTemplate();
            
        }
        
    }

?>