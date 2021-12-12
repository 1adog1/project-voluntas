<?php

    namespace Ridley\Views\Home;

    class Templates {
        
        protected function mainTemplate() {
            ?>
            
            <div class="row justify-content-center">
                <div class="col-md-6">
                    <div class="alert alert-primary text-center">
                        <h4 class="alert-heading">Welcome to Project Voluntas!</h4>
                        <hr>
                        This app is used to help authenticate users' Google Form submissions using Eve Online's SSO. 
                    </div>
                </div>
            </div>
            
            <?php
        }
        
        protected function metaTemplate() {
            ?>
            
            <title>Project Voluntas</title>
            
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
        
        public function renderMeta() {
            
            $this->metaTemplate();
            
        }
        
    }

?>