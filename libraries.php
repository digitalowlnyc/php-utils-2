<?php
  echo "Including phputils...";
  
  require_once("print.php");
  println("print.php loaded");
  require_once("errors.php");
  println("errors.php loaded");
  require_once("exec.php");
  println("exec.php loaded");
  require_once("download.php");
  println("download.php loaded");
  require_once("filesystem.php");
  println("filesystem.php loaded");
  
  println("Done loading phputils...");
