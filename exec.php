<?php 

function executeShell($command) {
 $output = [];
 $returnVar = null;
 println("PHP is going to execute: " . $command);
  exec($command, $output, $returnVar);
  println("Output is:");
  println($output);
  println("Return value is: ");
  println($returnVar);
}
