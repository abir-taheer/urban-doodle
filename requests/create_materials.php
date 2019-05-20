<?php
    $errors = [];
    $title = $_POST["title"];
    if( trim($title) === "" ){
        $errors[] = "Title cannot be empty";
    }
    if( strlen($title) > 128 ){
        $errors[] = "Title cannot be longer than 128 characters";
    }
    if( $_POST["type"] === "poster" ){
        if( strlen($_POST["extra"]) > 256 ){
            $errors[] = "Extra information cannot be longer than 256 characters";
        }
        if( $_FILES["poster"]["size"] > 2000000){
            $errors[] = "PDF is too large. Max size 2MB.";
        }
        if( $_FILES["poster"]["type"] !== "application/pdf" ){
            $errors[] = "File is not a pdf";
        }
    } elseif($_POST["type"] === "other"){
        if( strlen($_POST["content"]) > 2048){
            $errors[] = "Content cannot be longer than 2048 characters";
        }
    } else {
        $errors[] = "Invalid material type";
    }

    if( count($errors) === 0){
        $track = bin2hex(random_bytes(5));
        if( $_POST["type"] === "poster" ){
            $candidate = new Candidate($form["extra"]);

            // Try to see if GhostScript will accept the file
            exec('gs -sDEVICE=pdfwrite -dCompatibilityLevel=1.4 -dNOPAUSE -dBATCH -sOutputFile='.app_root."/public/static/elections/".$candidate->db_code."/materials/".$track.'.pdf '.$_FILES["poster"]["tmp_name"], $output, $fail);

            if( ! $fail ){
                // The file was sucessfully uploaded
                Database::secureQuery(
                    "INSERT INTO `materials`(`track`, `candidate_id`, `title`, `type`, `content`) VALUES (:track, :cand_id, :title, 'poster', :content)",
                    [
                        ":track"=>$track,
                        ":cand_id"=>$form["extra"],
                        ":title"=>$_POST["title"],
                        ":content"=>$_POST["extra"]
                    ]
                );
                $response["status"] = "success";
                $response["message"] = ["Your material has been sucessfully submitted for approval"];

            } else {
                unlink(app_root."/public/static/elections/".$candidate->db_code."/materials/".$track.'.pdf');
                $response["message"] = ["File could not be properly read. Please contact BOE if this keep happening"];
            }
        } elseif( $_POST["type"] === "other" ){
            Database::secureQuery(
                "INSERT INTO `materials`(`track`, `candidate_id`, `title`, `type`, `content`) VALUES (:track, :cand_id, :title, 'other', :content)",
                [
                    ":track"=>$track,
                    ":cand_id"=>$form["extra"],
                    ":title"=>$_POST["title"],
                    ":content"=>$_POST["content"]
                ]
            );
            $response["status"] = "success";
            $response["message"] = ["Your material has been sucessfully submitted for approval"];
        }
    } else {
        $response["message"] = $errors;
    }