var siteURL;

jQuery(document).ready(function () {
    
    var csrfToken = $("meta[name='csrftoken']").attr("content");
    
    $.ajaxSetup({
        beforeSend: function (request) {
            request.setRequestHeader("CSRF-Token", csrfToken);
        }
    });
    
    $("[data-bs-toggle=tooltip]").tooltip();
    
    infoModal = new bootstrap.Modal($("#info-modal"));
    
    $("#creation_button").click(function () {
        
        $(this).empty();
        $(this).append(
            $("<span/>")
                .addClass("spinner-border")
                .css({
                    "width": "16px", 
                    "height": "16px", 
                    "font-size": "12px"
                })
        );
        
        createPoll(
            String($("#poll_name").val()), 
            String($("#poll_date_start").val()), 
            String($("#poll_date_end").val()), 
            $("#is_anonymous").is(":checked"), 
            String($("input[name='restriction_type']:checked").val()), 
            parseInt($("#submission_limit").val()), 
            $("#core_access").val()
        );
        
    });
    
    $(".deletion_button").click(function () {
        
        $(this).empty();
        $(this).append(
            $("<span/>")
                .addClass("spinner-border")
                .css({
                    "width": "16px", 
                    "height": "16px", 
                    "font-size": "12px"
                })
        );
        
        deletePoll(
            $(this).attr("data-poll-id")
        );
        
    });
    
    $(".implement_button").click(function () {
        
        infoModal.show();
        
        getInfo(
            $(this).attr("data-poll-id")
        );
        
    });
    
    $("#submission_limit").on("input change", function () {
        $(".submission_limit_label").text($("#submission_limit").val());
    });
    
});

function timeFormatting(dateToCheck) {
    
    var currentDate = new Date();
    var secondsCounter = Math.abs(Math.floor((dateToCheck - currentDate) / 1000));
    
    if (secondsCounter <= 60) {
        return (secondsCounter + " Second(s)");
    }
    else if (secondsCounter <= 3600) {
        return (Math.floor(secondsCounter / 60) + " Minute(s)");
    }
    else if (secondsCounter <= 86400) {
        return (Math.floor(secondsCounter / 3600) + " Hour(s)");
    }
    else {
        return (Math.floor(secondsCounter / 86400) + " Day(s)");
    }
    
}

function createPoll(name, startDate, endDate, anonymity, limitType, submissionLimit, allowedCoreGroups) {
    
    $.ajax({
        url: "/manage/?core_action=api",
        type: "POST",
        data: {
            "Action": "Create_Poll", 
            "Name": name, 
            "Start Date": startDate, 
            "End Date": endDate, 
            "Anonymity": anonymity, 
            "Limit Type": limitType, 
            "Submission Limit": submissionLimit, 
            "Allowed Core Groups": allowedCoreGroups
        },
        mimeType: "application/json",
        dataType: "json",
        success: function(result) {
            
            location.reload();
            return false;
            
        },
        error: function(result) {
            
            $("#creation_button").empty();
            $("#creation_button").append(
                $("<i/>")
                    .addClass("bi bi-exclamation-triangle")
            );
            
        }
    });
    
}

function getInfo(pollID) {
    
    $("#user-auth-url").val("");
    $("#all-response-tokens").val("");
    $("#single-token").val("");
    
    $("#details-name").text("");
    
    $("#details-spinner").removeAttr("hidden");
    
    $.ajax({
        url: "/manage/?core_action=api",
        type: "POST",
        data: {"Action": "Get_Poll_Info", "ID": pollID},
        mimeType: "application/json",
        dataType: "json",
        success: function(result) {
            
            siteURL = result["Site URL"];
            
            var authURL = new URL("/authenticate/" + result["Poll ID"] + "/", siteURL);
            var allResponseURL = new URL("/verify/" + result["Poll Verification Token"] + "/?core_action=api", siteURL);
            var singleResponseURL = siteURL + "/verify/" + result["Poll Verification Token"] + "/\" & CELL_HERE & \"/?core_action=api";
            
            $("#details-spinner").attr("hidden", true);
            $("#details-name").text(result["Poll Name"]);
            
            $("#user-auth-url").val(authURL.toString());
            $("#all-response-tokens").val("=IMPORTDATA(\"" + allResponseURL.toString() + "\")");
            $("#single-token").val("=IMPORTDATA(\"" + singleResponseURL + "\")");
            
        },
        error: function(result) {
            
            
            
        }
    });
    
}
    
function deletePoll(idToDelete) {
    
    $.ajax({
        url: "/manage/?core_action=api",
        type: "POST",
        data: {"Action": "Delete_Poll", "ID": idToDelete},
        mimeType: "application/json",
        dataType: "json",
        success: function(result) {
            
            location.reload();
            return false;
            
        },
        error: function(result) {
            
            
            
        }
    });
    
}