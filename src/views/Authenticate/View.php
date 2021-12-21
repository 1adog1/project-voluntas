<?php

    namespace Ridley\Views\Authenticate;

    class Templates {
        
        protected function mainTemplate() {
            
            if ($this->model->hasAccess) {
                
                $this->accessGrantedTemplate();
                
            }
            else {
                
                $this->accessDeniedTemplate();
                
            }
            
        }
        
        protected function accessGrantedTemplate() {
            ?>
            
            <div class="row justify-content-center">
                <div class="col-md-6">
                    <?php $this->anonymityTemplate(); ?>
                    
                    <?php $this->tokenTemplate(); ?>
                </div>
            </div>
            
            <?php
        }
        
        protected function anonymityTemplate() {
            
            $is_anonymous = $this->model->pollDetails["Anonymity"];
            
            $anonymous_color = ($is_anonymous) ? "success" : "warning";
            $anonymous_icon = ($is_anonymous) ? "bi bi-eye-slash-fill": "bi bi-eye-fill";
            $anonymous_text = ($is_anonymous) ? "This poll is anonymous. Your response will not be associated with your name." : "This poll is NOT anonymous. Your name will be attached to your response.";
            
            ?>
            
            <div class="alert alert-<?php echo $anonymous_color; ?> text-center fw-bold">
                <i class="<?php echo $anonymous_icon; ?>"></i>
                <?php echo $anonymous_text; ?>
            </div>
            
            <?php
        }
        
        protected function tokenTemplate() {
            
            $user_token = $this->controller->userToken;
            $poll_name = $this->model->pollDetails["Name"];
            $poll_submission_limit = $this->model->pollDetails["Limit Amount"] . " Per " . $this->model->pollDetails["Limit Type"];
            $poll_start = (is_null($this->model->pollDetails["Start Time"])) ? "N/A" : date("F j, Y - G:i \U\T\C", $this->model->pollDetails["Start Time"]);
            $poll_end = (is_null($this->model->pollDetails["End Time"])) ? "N/A" : date("F j, Y - G:i \U\T\C", $this->model->pollDetails["End Time"]);
            
            ?>
            
            <div class="card alert-secondary text-center">
                <div class="card-body">
                    <h5>Your Verification Token</h5>
                    <p>Head back to the Google Form and submit this token to the authentication page: </p>
                    <div class="form-floating">
                        <input type="text" class="form-control" id="user_verification_token" value="<?php echo $user_token; ?>" readonly>
                        <label for="user_verification_token">Verification Token</label>
                    </div>
                </div>
            </div>
            
            <div class="card alert-secondary mt-3">
                <ul class="list-group list-group-flush">
                    <li class="list-group-item list-group-item-secondary">
                        <div class="small">Poll Name</div>
                        <div><?php echo $poll_name; ?></div>
                    </li>
                    <li class="list-group-item list-group-item-secondary">
                        <div class="small">Submission Limit</div>
                        <div><?php echo $poll_submission_limit; ?></div>
                    </li>
                    <li class="list-group-item list-group-item-secondary">
                        <div class="small">Start Time</div>
                        <div><?php echo $poll_start; ?></div>
                    </li>
                    <li class="list-group-item list-group-item-secondary">
                        <div class="small">End Time</div>
                        <div><?php echo $poll_end; ?></div>
                    </li>
                </ul>
            </div>
            
            <?php
        }
        
        protected function accessDeniedTemplate() {
            ?>
            
            <div class="row justify-content-center">
                <div class="col-md-6">
                    <div class="alert alert-warning">
                        <h4 class="alert-heading">You Cannot Access This Poll</h4>
                        <hr>
                        You're unable to access this poll, this could be for one of the following reasons: 
                        <ul class="mt-3">
                            <li>Your character doesn't meet the requirements to access this poll.</li>
                            <li>You have exceeded your response limit for this poll.</li>
                            <li>The poll hasn't started yet.</li>
                            <li>The poll has concluded.</li>
                            <li>The poll does not exist.</li>
                        </ul>
                    </div>
                </div>
            </div>
            
            <?php
        }
        
        protected function metaTemplate() {
            ?>
            
            <title>Authenticate a Poll</title>
            
            <?php
        }
        
    }

    class View extends Templates implements \Ridley\Interfaces\View {
        
        protected $controller;
        protected $model;
        
        public function __construct(
            private \Ridley\Core\Dependencies\DependencyManager $dependencies
        ) {
            
            $this->controller = $this->dependencies->get("Controller");
            $this->model = $this->dependencies->get("Model");
            
        }
        
        public function renderContent() {
            
            $this->mainTemplate();
            
        }
        
        public function renderMeta() {
            
            $this->metaTemplate();
            
        }
        
    }

?>