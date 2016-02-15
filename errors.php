function errorsOn() {
println("Error reporting has been turned on");
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
}
