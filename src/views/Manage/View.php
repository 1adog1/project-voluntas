<?php

    namespace Ridley\Views\Manage;

    class Templates {
        
        protected function mainTemplate() {
            
            $activeHider = (empty($this->model->polls["Active"])) ? "hidden" : "";
            $upcomingHider = (empty($this->model->polls["Upcoming"])) ? "hidden" : "";
            $completeHider = (empty($this->model->polls["Complete"])) ? "hidden" : "";
            $pollCounter = count($this->model->polls["Active"]) + count($this->model->polls["Upcoming"]) + count($this->model->polls["Complete"]);
            
            ?>
            
            <div class="row">
                
                <div class="col-md-3">
                    
                    <div class="d-grid">
                        
                        <button type="button" class="btn btn-outline-success btn-sm" data-bs-toggle="modal" data-bs-target="#creation-modal">
                            New Poll
                        </button>
                        
                    </div>
                    
                </div>
                <div class="col-md-6">
                </div>
                <div class="col-md-3 small text-end text-white fw-bold fst-italic">
                    
                    <span id="poll_counter"><?php echo $pollCounter; ?></span> Polls
                    
                </div>
                
            </div>
            
            <hr class="text-light">
            
            <div class="row justify-content-center text-center text-light">
                
                <div class="col-md-8">
                    
                    <h2 class="mt-3" id="active_polls_label" <?php echo $activeHider; ?>>Active Polls</h2>
                    
                    <div class="text-start" id="active_polls">
                        
                        <?php $this->pollLister("Active"); ?>
                        
                    </div>
                    
                    <h2 class="mt-3" id="upcoming_polls_label" <?php echo $upcomingHider; ?>>Upcoming Polls</h2>
                    
                    <div class="text-start" id="upcoming_polls">
                        
                        <?php $this->pollLister("Upcoming"); ?>
                        
                    </div>
                    
                    <h2 class="mt-3" id="complete_polls_label" <?php echo $completeHider; ?>>Complete Polls</h2>
                    
                    <div class="text-start" id="complete_polls">
                        
                        <?php $this->pollLister("Complete"); ?>
                        
                    </div>
                    
                </div>
            </div>
            
            <?php $this->infoModalTemplate(); ?>
            
            <?php $this->creationModalTemplate(); ?>
            
            <?php
        }
        
        protected function pollLister($category) {
            
            foreach ($this->model->polls[$category] as $eachPoll) {
                
                $anonymous_icon = ($eachPoll["anonymity"]) ? "bi bi-eye-slash-fill": "bi bi-eye-fill";
                
                if (!is_null($eachPoll["percorelimit"])) {
                    
                    $limit_text = ($eachPoll["percorelimit"] . " Per Core Account");
                    
                }
                elseif (!is_null($eachPoll["percharacterlimit"])) {
                    
                    $limit_text = ($eachPoll["percharacterlimit"] . " Per Character");
                    
                }
                else {
                    
                    $limit_text = "No Submission Limit";
                    
                }
                
                $startDate = (is_null($eachPoll["starttime"])) ? "N/A" : date("F j, Y - G:i \U\T\C", $eachPoll["starttime"]);
                $endData = (is_null($eachPoll["endtime"])) ? "N/A" : date("F j, Y - G:i \U\T\C", $eachPoll["endtime"]);
                
                ?>
                
                <div class="card alert-secondary mt-3">
                
                    <div class="card-header h5">
                        <i class="<?php echo $anonymous_icon; ?>"></i>
                        <?php echo htmlspecialchars($eachPoll["name"]); ?>
                    </div>
                
                    <div class="row">
                        
                        <div class="col-xxl-4">
                            
                            <ul class="list-group list-group-flush">
                                
                                <li class="list-group-item list-group-item-secondary">
                                    
                                    <div class="small">Start Time</div>
                                    
                                    <div><?php echo htmlspecialchars($startDate); ?></div>
                                    
                                </li>
                                
                            </ul>
                            
                        </div>
                        
                        <div class="col-xxl-4">
                            
                            <ul class="list-group list-group-flush">
                                
                                <li class="list-group-item list-group-item-secondary">
                                    
                                    <div class="small">End Time</div>
                                    
                                    <div><?php echo htmlspecialchars($endData); ?></div>
                                    
                                </li>
                                
                            </ul>
                            
                        </div>
                        
                        <div class="col-xxl-4">
                            
                            <ul class="list-group list-group-flush">
                                
                                <li class="list-group-item list-group-item-secondary">
                                    
                                    <div class="small">Submission Limit</div>
                                    
                                    <div><?php echo htmlspecialchars($limit_text); ?></div>
                                    
                                </li>
                                
                            </ul>
                            
                        </div>
                    
                    </div>
                    
                    <hr class="text-secondary ms-3 me-3 mt-1 mb-1">
                    
                    <div class="row">
                        
                        <div class="col-xxl-3">
                            
                            <ul class="list-group list-group-flush">
                                
                                <li class="list-group-item list-group-item-secondary">
                                
                                    <div class="d-grid">
                                        
                                        <button type="button" class="btn btm-sm btn-outline-dark mt-2 implement_button" data-poll-id="<?php echo htmlspecialchars($eachPoll["id"]); ?>">Implement Poll</button>
                                        
                                    </div>
                                    
                                </li>
                                
                            </ul>
                            
                        </div>
                        
                        <div class="col-xxl-6 text-center">
                    
                            <ul class="list-group list-group-flush">
                                
                                <li class="list-group-item list-group-item-secondary">
                                    
                                    <?php if (!is_null($eachPoll["allowedroles"])) { ?>
                                    
                                        <div class="small">Authorized Core Groups</div>
                                        
                                        <div>
                                            
                                            <?php foreach (json_decode($eachPoll["allowedroles"]) as $eachRole) { ?>
                                                
                                                <span class="badge bg-secondary"><?php echo htmlspecialchars($eachRole); ?></span>
                                                
                                            <?php } ?>
                                            
                                        </div>
                                    
                                    <?php } ?>
                                    
                                </li>
                                
                            </ul>
                            
                        </div>
                        
                        <div class="col-xxl-3">
                            
                            <ul class="list-group list-group-flush">
                                
                                <li class="list-group-item list-group-item-secondary">
                                
                                    <div class="d-grid">
                                        
                                        <button type="button" class="btn btm-sm btn-outline-danger mt-2 deletion_button" data-poll-id="<?php echo htmlspecialchars($eachPoll["id"]); ?>">Delete Poll</button>
                                        
                                    </div>
                                    
                                </li>
                                
                            </ul>
                            
                        </div>
                        
                    </div>
                    
                </div>
                
                <?php
            }
            
        }
        
        protected function infoModalTemplate() {
            ?>
            
            <div id="info-modal" class="modal fade" tabindex="-1" aria-hidden="true">
                
                <div class="modal-dialog modal-xl">
                    
                    <div class="modal-content bg-dark text-light border-secondary">
                        
                        <div class="modal-header border-secondary">
                            
                            <h5 class="modal-title">Poll Details - <span id="details-name"></span> <span id="details-spinner" class="spinner-border" style="height: 16px; width: 16px; font-size: 12px;"></span></h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            
                        </div>
                        <div class="modal-body">
                            
                            <div class="alert alert-success text-center mt-3">
                                
                                <span class="fw-bold">In your poll, create a short answer question for users to place their response tokens. Direct them to the URL below using the question description so they can receive said token.</span>
                                
                            </div>
                            
                            <hr>
                            
                            <div class="h4">User Authentication</div>
                            
                            <div class="row">
                                
                                <div class="col-3 text-end">
                                    
                                    <label for="user-auth-url" class="col-form-label">User Authentication URL</label>
                                    
                                </div>
                                
                                <div class="col-9">
                                    
                                    <input type="text" id="user-auth-url" class="form-control" placeholder="Loading..." readonly>
                                    
                                </div>
                                
                            </div>
                            
                            <hr>
                            
                            <div class="alert alert-danger text-center mt-3">
                                
                                <i class="bi bi-exclamation-triangle"></i>
                                <span class="fw-bold">The formulas below contain your private verification token! Do not share them!</span>
                                
                            </div>
                            
                            <hr>
                            
                            <div class="h4">Response Verification</div>
                            
                            <div class="row mt-3">
                                
                                <div class="col-3 text-end">
                                    
                                    <label for="all-response-tokens" class="col-form-label">Get All Response Tokens</label>
                                    
                                </div>
                                
                                <div class="col-9">
                                    
                                    <input type="text" id="all-response-tokens" class="form-control" placeholder="Loading..." readonly>
                                    
                                </div>
                                
                            </div>
                            
                            <div class="row align-items-center mt-3">
                                
                                <div class="col-3 text-end">
                                    
                                    <label for="single-token" class="col-form-label">Validate Single Response Token</label>
                                    
                                </div>
                                
                                <div class="col-7">
                                    
                                    <input type="text" id="single-token" class="form-control" placeholder="Loading..." readonly>
                                    
                                </div>
                                
                                <div class="col-2">
                                    
                                    <div class="form-text mb-1">Replace CELL_HERE with the cell in question.</div>
                                    
                                </div>
                                
                            </div>
                            
                            <hr>
                            
                        </div>
                        
                    </div>
                    
                </div>
                
            </div>
            
            <?php
        }
        
        protected function creationModalTemplate() {
            
            $coreDisabler = ($this->configVariables["Auth Type"] == "Neucore") ? "" : "disabled";
            ?>
            
            <div id="creation-modal" class="modal fade" data-bs-backdrop="static" tabindex="-1" aria-hidden="true">
                
                <div class="modal-dialog modal-xl">
                    
                    <div class="modal-content bg-dark text-light border-secondary">
                        
                        <div class="modal-header border-secondary">
                            
                            <h5 class="modal-title">Create a New Poll</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            
                        </div>
                        <div class="modal-body">
                            
                            <div class="row">
                                
                                <div class="col-xl-4">
                                    
                                    <div class="form-floating">
                                        <input id="poll_name" type="text" class="form-control" value="">
                                        <label class="text-dark" for="poll_name">Poll Name</label>
                                    </div>
                                    
                                </div>
                                
                                <div class="col-xl-3">
                                    
                                    <div class="form-floating">
                                        <input type="date" class="form-control" name="poll_date_start" id="poll_date_start" value="">
                                        <label class="text-dark" for="poll_date_start">Start Date</label>
                                    </div>
                                    
                                </div>
                                
                                <div class="col-xl-3">
                                    
                                    <div class="form-floating">
                                        <input type="date" class="form-control" name="poll_date_end" id="poll_date_end" value="">
                                        <label class="text-dark" for="poll_date_end">End Date</label>
                                    </div>
                                    
                                </div>
                                
                                <div class="col-xl-2 mt-3">
                                    
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="is_anonymous">
                                        <label class="form-check-label" for="is_anonymous">Anonymous</label>
                                    </div>
                                    
                                </div>
                                
                            </div>
                            
                            <div class="row mt-4">
                            
                                <div class="col-xl-4">
                                    
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="restriction_type" id="character_restriction" value="Character" checked>
                                        <label class="form-check-label" for="character_restriction"><span class="submission_limit_label">1</span> Submission(s) Per Character</label>
                                    </div>

                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="restriction_type" id="core_account_restriction" value="Core" <?php echo $coreDisabler; ?>>
                                        <label class="form-check-label" for="core_account_restriction"><span class="submission_limit_label">1</span> Submission(s) Per Core Account</label>
                                    </div>
                                    
                                </div>
                                
                                <div class="col-xl-8">
                                    
                                    <label class="form-label" for="submission_limit">Submission Limit</label>
                                    <input type="range" class="form-range" min="1" max="10" step="1" value="1" id="submission_limit">
                                    
                                </div>
                                
                            </div>
                            
                            <?php $this->groupSelectionTemplate(); ?>
                            
                            <div class="d-grid mt-4">
                            
                                <button id="creation_button" class="btn btn-outline-success">Create</button>
                                
                            </div>
                            
                        </div>
                        
                    </div>
                    
                </div>
                
            </div>
            
            <?php
        }
        
        protected function groupSelectionTemplate() {
            
            if ($this->configVariables["Auth Type"] == "Neucore") {
                ?>
                <label for="core_access" class="form-label mt-4">Allowed Core Groups <a class="text-light" href="#" data-bs-toggle="tooltip" data-bs-placement="right" title="Any requirement here is in addition to the user having the site-wide 'Submitter' access role."><i class="bi bi-info-circle"></i></a></label>
                <select id="core_access" class="form-select" aria-label="core group access" multiple>
                    
                    <?php foreach ($this->controller->coreGroups as $eachGroup) { ?>
                        
                        <option value="<?php echo $eachGroup; ?>"><?php echo $eachGroup; ?></option>
                        
                    <?php } ?>
                    
                </select>
                
                <?php
            }
            
        }
        
        protected function metaTemplate() {
            ?>
            
            <title>Poll Management</title>
            
            <script src="/resources/js/Manage.js"></script>
            
            <?php
        }
        
    }

    class View extends Templates implements \Ridley\Interfaces\View {
        
        protected $controller;
        protected $model;
        protected $configVariables;
        
        public function __construct(
            private \Ridley\Core\Dependencies\DependencyManager $dependencies
        ) {
            
            $this->controller = $this->dependencies->get("Controller");
            $this->model = $this->dependencies->get("Model");
            $this->configVariables = $this->dependencies->get("Configuration Variables");
            
        }
        
        public function renderContent() {
            
            $this->mainTemplate();
            
        }
        
        public function renderMeta() {
            
            $this->metaTemplate();
            
        }
        
    }

?>