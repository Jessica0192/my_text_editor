<?php
    /*
        FILE: serverSide.php
        PROJECT: Creating a Basic Text Editor using jQuery and JSON
        PROGRAMMER: Jessica Sim
        FIRST VERSION: 2020-12-04
        DESCRIPTION: 
            This script is the server side page that when the request is submitted, it does some work and sends back the right 
            response for each requests. 
    */
    header("Content-Type: application/json");


    /*
    FUNCTION: *There's no function and function name
    DESCRIPTION:
        When the request is sent from the client side, firstly, it validates what is a request method.
        If the request method is "GET", it reads all the file names in the directory called "MyFiles" and
        send back the list of file names to client after encode the string in JSON. I want to mention that 
        if the request is sent, firstly it decode the data which is passed from client using JSON and when 
        it's sending back the response, it decodes the data using JSON as well. Them, if the request method
        is POST, it decodes the passed data and see what is the value of 'cmd'. If cmd is 1, it saves the 
        text which is passed to a specified file. If cmd equals 2, create a file with a name that user has specified
        and write the content. If cmd is none of them, it reads the file which user has specified as well and send 
        back the contents of file.
    PARAMETERS: 
        no parameters
    RETURNS:
        there are some return values depends on cases:
        IF REQUEST_METHOD: GET
            then returns the list of file names if there is an error sends "ERROR"
        ELSE
            IF cmd = 1
                then returns "SAVE COMPLETED", if failed, returns nothing
            ELSE IF cmd = 3
                then returns "SUCCESSFULLY CREATED A FILE!!", if failed, returns nothing
            ELSE
                returns the file contents
    */
    switch($_SERVER['REQUEST_METHOD'])
    {
        case 'GET':
            $fileNames = array();
            $path = "MyFiles/";
            if(is_dir($path)){
                if($dir = opendir($path))
                {
                    while(($file = readdir($dir)) != false)
                    {
                        if($file != "." && $file != "..")
                        {
                            $fileNames[]->name = $file;
                        }
                    }
                    $JSONfiles = json_encode($fileNames);
                    echo $JSONfiles;
                }
            }
            else
            {
                echo "ERROR";
            }
        break;
        case 'POST':
            $data = file_get_contents('php://input');
            $decodeData = json_decode($data);
            if($decodeData->cmd == 1)
            {
                $dir = 'MyFiles/'.$decodeData->fName;
                $fcontent = $decodeData->content;
                $myfile = fopen($dir,"w") or die("Unable to open file!");
                fwrite($myfile, $fcontent);
                fclose($myfile);
                echo "SAVE COMPLETED!!";
            }
            else if($decodeData->cmd == 2)
            {
                $dirSaveAs = 'MyFiles/'.$decodeData->fnameSaveAs;
                $fcontentSaveAs = $decodeData->contentSaveAs;
                $myfileSaveAs = fopen($dirSaveAs,"w");
                fwrite($myfileSaveAs, $fcontentSaveAs);
                fclose($myfileSaveAs);
                $passData['success'] = "SUCCESSFULLY CREATED A FILE!!";
                $passData['newFile'] = array();
                $path = "MyFiles/";
                    if(is_dir($path)){
                        if($dir = opendir($path))
                        {
                            while(($file = readdir($dir)) != false)
                            {
                                if($file != "." && $file != "..")
                                {
                                    $passData['newFile'][] = $file;
                                }
                            }
                        }
                    }
                echo json_encode($passData);
            }
            else
            {
                $f = file_get_contents('php://input');
                function isValidJSON($f) {
                    json_decode($f);
                    return json_last_error() == JSON_ERROR_NONE;
                }
                $selectedFileName = json_decode($f);
                $fs = 'MyFiles/'.$selectedFileName;
                if(file_exists($fs))
                {
                    $file = fopen($fs, "r");
                    while(!feof($file))
                    {
                        $data = fgets($file);
                        $str = $str.$data;
                    }
                    $JSONfileData = json_encode($str);
                    fclose($file);
                    echo $JSONfileData;
                }
            }
            
        break;
    }
?>
