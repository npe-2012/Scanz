<?php
    /*
     * Author : Michel Parpaillon
     * Site : www.michelparpaillon.com
     */

    require 'DropboxUploader.php';

    // Dropbox account info
    $dbox_email  = "name@mail.com"; // @edit
    $dbox_pass   = "pass";          // @edit
    $dbox_certif = "cacert.pem";

    // DB Connection
    $db_host = "localhost";
    $db_port = "3306";
    $db_name = "scanz";
    $db_user = "scanz";
    $db_pass = "";

    try
    {
        $db_conn = new PDO("mysql:host=$db_host;port=$db_port;dbname=$db_name", $db_user, $db_pass);
    }
    catch (Exception $e)
    {
        die("PDO Connection failed, N°".$e->getCode()." : ".$e->getMessage());
    }

    // DropboxUploader init
    $uploader = new DropboxUploader($dbox_email, $dbox_pass);
    $uploader->setCaCertificateFile($dbox_certif);

    $req = $db_conn->prepare("SELECT m.id, m.label, m.site_url, m.unread FROM manga m");
    $req->execute();

    while ($manga = $req->fetch(PDO::FETCH_OBJ))
    {
        $url_chapter = $manga->site_url."/$manga->unread";
        
        // We check if the nex chapter is out (404 means no)
        $headers = get_headers($url_chapter);
        if (substr($headers[0], 9, 3) != "404")
        {
            // We loop on each page and download it if exists
            $i = 1;
            $is404 = false;

            do {
                $page = $i < 10 ? "0$i" : $i;
                $filename = "$page.jpg";
                $url = "$url_chapter/$filename";

                $path_to_chapter = "/Scanz/".$manga->label."/$manga->unread";

                // Does the url return an image ?
                if (@getimagesize($url))
                {
                    file_put_contents($filename, file_get_contents($url));
                    $uploader->upload($filename, $path_to_chapter);
                    unlink($filename);
                }
                else
                {
                    $is404 = true;

                    // Download is now finished
                    // We create log
                    $log = $manga->label." Chapter ".$manga->unread." downloaded";
                    $db_conn->exec("INSERT INTO log (label, created_at) VALUE ('".$log."', NOW())");

                    // We ++ unread value
                    $next_chapter = $manga->unread + 1;
                    $db_conn->exec("UPDATE manga SET unread = $next_chapter WHERE id = ".$manga->id);
                }

                $i++;
                
            } while (!$is404);
        }
    }
?>