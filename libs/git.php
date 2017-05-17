<?php
/**
 * Creator: Bryan Mayor
 * Company: Blue Nest Digital, LLC
 * Date: 11/26/16
 * Time: 3:40 AM
 * License: (All rights reserved)
 */

function getGit($user, $project, $directory = null) {
    $url = "https://github.com/" . $user . "/" . $project . ".git";
    if($directory !== null) {
        echo shell_exec('git clone ' . $url . '');
    } else {
        echo shell_exec('git clone ' . $url . ' ' . $directory);
    }
}

function deployGit($user, $project, $directory) {
    if(!file_exists("deploy_files")) {
        mkdir('deploy_files');
    }
    chdir('deploy_files');
    getGit($user, $project, $directory);
    copy($directory . '/dist', '../' . $project);
}