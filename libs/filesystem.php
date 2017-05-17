<?php 

/**
 * Move/rename file or directory. 
 * 
 * NB: 'native' mode doesn't seem to work with non-empty directories.
 * Use 'shell' mode instead for this case.
 * 
 * @param $oldDir
 * @param $newDir
 * @param string $mode Native or using shell command
 * @throws Exception
 */
function renameDirectory($oldDir, $newDir, $mode = "native") {
    if($mode === "native") {
        $result = rename($oldDir, $newDir);

        if($result === false) {
            println("Could not rename $oldDir to $newDir");
        } else {
            println("Renamed $oldDir to $newDir");
        }
    } else if($mode === "shell") {
        println("Using 'mv' command to move files'");
        executeShell("mv $oldDir $newDir");
        
        $newFileExists = file_exists($newDir);
        
        if($newFileExists === false) {
            println("Error moving file: new file exists?: $newFileExists");
        }
        
    } else {
        throw new Exception("Unsupported rename mode: $mode");
    }
}

/**
 * Allows you to put a file even if the directory doesn't exist
 *
 * @param $location
 * @param $data
 * @param int $permissions
 * @return int
 * @throws Exception
 */
function filePut($location, $data, $permissions = 0775, $verbose = false) {
    $dir = dirname($location);
    $dirs = explode("/", $dir);
/*
    if(!is_writable($dir)) {
        die("filePut: Error - Directory is not writable: $dir");
    }
*/
    $curDir = "";
    while(count($dirs)) {
        $curDir .= "/" . array_shift($dirs);
        if(!is_dir($curDir)) {
            try {
                if(!mkdir($curDir, $permissions)) {
                    $cwd = getcwd();
                    throw new Exception("filePut: Could not make directory (zero returned): " . $curDir . " in " . $cwd);
                } else {
                    if($verbose) {
                        echo "Made: " . $curDir . "<br>";
                    }
                }
            } catch(Exception $e) {
                $cwd = getcwd();
                throw new Exception("filePut: Could not make directory (exception): " . $curDir . " in " . $cwd);
            }
        }
    }

    return file_put_contents($location, $data);
}

// Credit: http://php.net/manual/en/function.filesize.php#106569
function bytesToString($byteCount, $decimals = 2) {
    $sz = 'BKMGTP';
    $factor = floor((strlen($byteCount) - 1) / 3);
    return sprintf("%.{$decimals}f", $byteCount / pow(1024, $factor)) . @$sz[$factor];
}

function fileGetSize($filename, $abbreviate = true) {
    if($filename === null || strlen($filename) === 0)
        throw new Exception("Invalid input for fileGetSize: " . $filename);
    $sizeInBytes = filesize($filename);

    if($abbreviate)
        $size = bytesToString($sizeInBytes);
    else
        $size = (String)$sizeInBytes;

    return $size;
}

/**
 * Helper for turning relative links/hrefs into fully qualified paths
 * @param $requestPath
 * @param $relativeUrl
 * @return array
 */
function relPathToAbsolute($requestPath, $relativeUrl) {

    $parsedRequestPath = parse_url($requestPath);

    $uri = valueOrDefault($parsedRequestPath, 'path', "/defaultpath");
    $urlComponents = explode("/", $uri);

    $relativeDirectories = explode("/", $relativeUrl);

    $realPath = [];
    $directoryDepthIdx = count($urlComponents) - 1;

    /*
     * This resolves the ".." and "." components to turn them
     * into and actual directory.
     */
    foreach($relativeDirectories as $relativeDirectory) {
        if($relativeDirectory == "..") {
            $directoryDepthIdx--;
        } else if($relativeDirectory == ".") {

        } else {
            $realPath[] = $relativeDirectory;
        }
    }

    $resolvedDirectory = array_slice($urlComponents, 0, $directoryDepthIdx);
    $computedPath = implode("/", $resolvedDirectory) . "/" .  implode("/", $realPath);

    $resultUrl = $parsedRequestPath['scheme'] . "://" . $parsedRequestPath['host'];
    if(isset($parsedRequestPath['port']) && strlen($parsedRequestPath['port'])) {
        $resultUrl .= ":" . $parsedRequestPath['port'];
    }

    $resultUrl .= $computedPath;
    //echo "Original: " . $relativeDirectory . ", resolved to, " . $resultUrl);
    return [
        "qualified-url" => $resultUrl,
        "path" => $computedPath
    ];
}

function urlToFilenameSanitize($filename) {
    $filename = str_replace("\n", "", $filename);
    $filename = str_replace(".", "-", $filename);
    $filename = str_replace("", "", $filename);
    $filename = str_replace("/", "_", $filename);
    $filename = str_replace("\\", "_", $filename);
    $filename = str_replace("www", "site", $filename);
    if(preg_match('/^[a-z0-9-]+\.[a-z0-9]$/', $filename) === false) {
        throw new Exception("Invalid filename: " . $filename);
    }

    return $filename;
}

function makeDirectoryVerbose($dir, $recursive = true, $mode = 0777) {
    makeDirectory($dir, $recursive, $mode);
    echo 'Created directory: ' . $dir . NL;
}

function makeDirectory($dir, $recursive = true, $mode = 0777) {
    if(!mkdir($dir, $mode, $recursive)) {
        throw new Exception('Could not create directory: ' . $dir);
    }
}