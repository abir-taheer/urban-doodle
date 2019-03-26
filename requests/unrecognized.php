<?php
$user = Session::getUser();
$id = Session::getIdInfo();
switch($form['extra']){
    case "submit":
        //This is the only case in which the user is allowed to submit the form
        if( $user->status === -1 ){
            //do some validation to make sure that the form was correctly filled out
            $allowed_grades = array("9", "10", "11", "12", "f");
            if( $_POST['full_name'] === "" OR
                $_POST['grade'] === "" OR
                $_POST['osis'] === ""
            ) {
                $errors[] = "Form was not filled out completely!";
            }
            if( ! in_array( $_POST['grade'], $allowed_grades ) ){
                $errors[] = "Invalid value for grade field!";
            }
            if( !is_numeric($_POST['osis']) OR strlen($_POST['osis']) !== 9 ){
                $errors[] = "Invalid value for osis field!";
            }

            if( count($errors) === 0 ){
                //add the instance to the database and send out an email
                $track = strtoupper(bin2hex(random_bytes(4)));
                $name = $_POST['full_name'];
                $email = $id['email'];
                $grade = ($_POST['grade'] === "f") ? "Faculty" : $_POST['grade'];
                $osis = $_POST['osis'];
                $date = new DateTime("now", new DateTimeZone(app_time_zone));
                $created = Web::getUTCTime()->format("Y-m-d H:i:s");
                try{
                    Database::secureQuery(
                        "INSERT INTO `unrecognized_emails` (`track`, `name`, `email`, `grade`, `osis`, `created`) VALUES (:track, :full_name, :email, :grade, :osis, :created)",
                        array(
                            ":track"=>$track,
                            ":full_name"=>$name,
                            ":email"=>$email,
                            ":grade"=>$grade,
                            ":osis"=>$osis,
                            ":created"=>$created
                    ), null);

                    $response['status'] = "success";
                    $response['message'][] = "Your request has been received successfully. You will receive a confirmation email and a follow up email after the request has been approved.";
                    //now send out an email
                    $em = new Email();
                    $em->to = array($email);
                    $em->cc = User::getAdminEmails("u_e");
                    $em->subject = "New Unrecognized Email Request: ".$track;
                    $em->body = "
                        This email is to confirm that we received a request from you to add your email address to the voting database. 
                        The information pertaining to your request is as follows: <br>
                        <br>Request ID: ".htmlspecialchars($track)."
                        <br>Name: ".htmlspecialchars($grade)."
                        <br>Email Address: ".htmlspecialchars($email)."
                        <br>Grade: ".htmlspecialchars($grade)."
                        <br>Osis: ".htmlspecialchars($osis)."
                        <br>Submitted At: ".htmlspecialchars($date->format("F d, Y  h:ia"))."
                        <br><br>This is just a confirmation. You will receive a follow up email once your request has been approved.";
                    $em->send();
                } catch(Exception $e){
                    $response['status'] = "error";
                    $response['message'][] = "There was an unexpected error when submitting your form. Please contact us if this continues. The error is as follows: ".$e;
                }

            } else {
                //their form was not filled out correctly, echo out the error messages
                $response['status'] = "error";
                $response['message'] = $errors;
            }
        } else {
            $response['status'] = "error";
            $response['message'][] = "You do not have sufficient ability to perform that action";
        }
        break;
}